<?php
require_once __DIR__ . '/VtDataDir.php';

/**
 * PleskDomains — WordPress site URLs from Plesk.
 *
 * Role-based filtering:
 *   - Admin   : all WordPress domains (MySQL scan)
 *   - Reseller: their own + their clients' domains ∩ WordPress sites
 *   - Client  : their own domains ∩ WordPress sites
 *
 * Reseller/client filtering uses the Plesk `psa` MySQL database directly —
 * pm_Domain::getAllDomains() does not reliably respect session context in
 * extension modules, so we query ownership ourselves.
 */
class PleskDomains
{
    const DOMAIN_CACHE_TTL  = 86400; // 1 day
    private static function domainCacheFile(): string { return VtDataDir::path('vt_domains.json'); }

    /** Request-level cache: repeated calls within one HTTP request are free */
    private static ?array $requestCache = null;

    /** Return WordPress domain hostnames for the current Plesk user */
    public static function forCurrentUser(): array
    {
        if (self::$requestCache !== null) return self::$requestCache;

        // pm_Session is loaded after pm_Context::init() in the Plesk web context
        if (class_exists('pm_Session')) {
            try {
                self::$requestCache = self::fromPlesk();
                return self::$requestCache;
            } catch (\Throwable $e) {
                // Could not determine user; fall through to full list
            }
        }

        self::$requestCache = self::allWordPressDomains();
        return self::$requestCache;
    }

    /** All WordPress domains on this server (for admin and cron) */
    public static function allWordPressDomains(): array
    {
        if (file_exists(self::domainCacheFile())
            && (time() - filemtime(self::domainCacheFile())) < self::DOMAIN_CACHE_TTL) {
            $d = json_decode(file_get_contents(self::domainCacheFile()), true);
            if (is_array($d) && $d) return $d;
        }

        $domains = self::fetchFromMysql();
        file_put_contents(self::domainCacheFile(), json_encode($domains));
        return $domains;
    }

    public static function invalidateCache(): void
    {
        self::$requestCache = null;
        @unlink(self::domainCacheFile());
    }

    // ── Private ─────────────────────────────────────────────────

    private static function fromPlesk(): array
    {
        $client = pm_Session::getClient();
        if (!$client) throw new \Exception('No Plesk session client');

        // Admin sees all WordPress sites
        if ($client->isAdmin()) {
            return self::allWordPressDomains();
        }

        // Reseller or Client: query their Plesk domains from PSA database,
        // then intersect with the WordPress site list.
        $allWp       = self::allWordPressDomains();
        $userDomains = self::pleskDomainsForUser($client->getLogin(), $client->isReseller());

        // null = MySQL unavailable (safe fallback: show nothing rather than everything)
        // []   = user has no domains — correct, show empty dashboard
        if ($userDomains === null) {
            return [];
        }

        $userSet = array_flip($userDomains);
        $result  = array_values(array_filter($allWp, fn($d) => isset($userSet[$d])));
        sort($result);
        return $result;
    }

    /**
     * Query Plesk PSA database for domains owned by a given login.
     * Returns null on failure (DB unavailable / login not found),
     * or an array of domain names (possibly empty if user has no domains).
     * Resellers get their own subscriptions + all direct sub-clients' domains.
     */
    private static function pleskDomainsForUser(string $login, bool $isReseller): ?array
    {
        $pdo = self::pleskPdo();
        if (!$pdo) return null; // DB unavailable

        try {
            $stmt = $pdo->prepare('SELECT id FROM clients WHERE login = ? LIMIT 1');
            $stmt->execute([$login]);
            $clientId = $stmt->fetchColumn();
            if (!$clientId) return null; // Login not found in Plesk DB

            if ($isReseller) {
                // Reseller: own subscriptions + all direct sub-clients' domains
                $stmt = $pdo->prepare(
                    'SELECT LOWER(d.name) FROM domains d
                     WHERE d.cl_id = ?
                        OR d.cl_id IN (SELECT id FROM clients WHERE parent_id = ?)
                     ORDER BY d.name'
                );
                $stmt->execute([$clientId, $clientId]);
            } else {
                // Client: own domains only
                $stmt = $pdo->prepare(
                    'SELECT LOWER(d.name) FROM domains d
                     WHERE d.cl_id = ?
                     ORDER BY d.name'
                );
                $stmt->execute([$clientId]);
            }

            return $stmt->fetchAll(\PDO::FETCH_COLUMN) ?: []; // empty array = no domains
        } catch (\PDOException $e) {
            return null; // Query failed
        }
    }

    private static function pleskPdo(): ?\PDO
    {
        $passFile = '/etc/psa/.psa.shadow';
        if (!file_exists($passFile)) return null;
        try {
            $pass = trim(file_get_contents($passFile));
            return new \PDO(
                'mysql:host=localhost;dbname=psa;charset=utf8',
                'admin',
                $pass,
                [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]
            );
        } catch (\PDOException $e) {
            return null;
        }
    }

    private static function fetchFromMysql(): array
    {
        $passFile = '/etc/psa/.psa.shadow';
        if (!file_exists($passFile)) return [];

        $script = VtDataDir::path('vt_get_domains.sh');
        file_put_contents($script, <<<'BASH'
#!/bin/bash
PASS=$(cat /etc/psa/.psa.shadow)
MYSQL="mysql -uadmin -p${PASS} -Ns"
TABLES=$($MYSQL -e "
SELECT TABLE_SCHEMA, TABLE_NAME
FROM information_schema.TABLES
WHERE TABLE_NAME LIKE '%options'
  AND TABLE_SCHEMA NOT IN ('information_schema','mysql','performance_schema','sys','psa','horde','roundcube')
ORDER BY TABLE_SCHEMA;" 2>/dev/null)
while IFS=$'\t' read -r db tbl; do
    URL=$($MYSQL -e "SELECT option_value FROM \`${db}\`.\`${tbl}\` WHERE option_name='siteurl' LIMIT 1;" 2>/dev/null)
    [ -n "$URL" ] && echo "$URL"
done <<< "$TABLES"
BASH
        );
        chmod($script, 0755);
        $output = shell_exec("bash $script 2>/dev/null") ?? '';

        $domains = [];
        $seen    = [];
        foreach (explode("\n", $output) as $url) {
            $url = preg_replace('#^https?://(www\.)?#', '', rtrim(strtolower(trim($url)), '/'));
            if (!$url || isset($seen[$url])) continue;
            if (!preg_match('/^[a-z0-9.\-]+\.[a-z]{2,}$/', $url)) continue;
            if (preg_match('/localhost|127\.|\.local$|\.test$/', $url)) continue;
            $seen[$url] = true;
            $domains[]  = $url;
        }
        sort($domains);
        return $domains;
    }
}
