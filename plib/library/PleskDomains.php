<?php
require_once __DIR__ . '/VtDataDir.php';

/**
 * PleskDomains: every active domain and subdomain with web hosting in Plesk.
 *
 * Read straight from Plesk's `psa` database (domains with htype 'vrt_hst' and
 * status 0). Forwarding-only domains, domains without hosting and suspended or
 * disabled domains are skipped: there is no website of ours to check there.
 *
 * Role-based filtering:
 *   - Admin   : all hosted domains
 *   - Reseller: their own + their clients' hosted domains
 *   - Client  : their own hosted domains
 *
 * WordPress sites on a hostname under a hosted domain that is not a Plesk
 * subdomain of its own (e.g. 2025.example.com served by example.com) are added
 * too, found through the `siteurl` option of WordPress databases.
 *
 * pm_Domain::getAllDomains() does not reliably respect session context in
 * extension modules, so ownership is queried from the psa database as well.
 */
class PleskDomains
{
    const DOMAIN_CACHE_TTL = 86400; // 1 day: WordPress database scan (slow)
    const LIST_CACHE_TTL   = 600;   // 10 min: hosted-domain list (cheap), so disabled sites drop out quickly
    private static function domainCacheFile(): string { return VtDataDir::path('vt_domains.json'); }
    private static function wpCacheFile(): string     { return VtDataDir::path('vt_wp_sites.json'); }

    /** Request-level cache: repeated calls within one HTTP request are free */
    private static ?array $requestCache = null;

    /** Hosted domain names visible to the current Plesk user ($wpOnly: WordPress sites only) */
    public static function forCurrentUser(bool $wpOnly = false): array
    {
        if (self::$requestCache === null) self::$requestCache = self::discoverForCurrentUser();
        return $wpOnly ? self::onlyWordPress(self::$requestCache) : self::$requestCache;
    }

    /**
     * Site list from the cache files only, never running discovery (for the Home card).
     * Returns null when nothing is cached yet.
     */
    public static function cachedDomains(bool $wpOnly = false): ?array
    {
        $d = is_file(self::domainCacheFile()) ? json_decode((string)file_get_contents(self::domainCacheFile()), true) : null;
        if (!is_array($d) || !$d) return null;
        if (!$wpOnly) return $d;
        $wp = is_file(self::wpCacheFile()) ? json_decode((string)file_get_contents(self::wpCacheFile()), true) : null;
        return is_array($wp) ? array_values(array_intersect($d, $wp)) : $d;
    }

    private static function discoverForCurrentUser(): array
    {

        // pm_Session is loaded after pm_Context::init() in the Plesk web context
        if (class_exists('pm_Session')) {
            try {
                return self::fromPlesk();
            } catch (\Throwable $e) {
                // Could not determine user; fall through to full list
            }
        }

        return self::allDomains();
    }

    /** All hosted domains on this server (for admin and the nightly scan), cached for a day */
    public static function allDomains(bool $wpOnly = false): array
    {
        $all = self::allHostedDomains();
        return $wpOnly ? self::onlyWordPress($all) : $all;
    }

    /** Keep only the hostnames that run WordPress */
    public static function onlyWordPress(array $domains): array
    {
        $wp = array_flip(self::wordPressHosts());
        return array_values(array_filter($domains, fn($d) => isset($wp[$d])));
    }

    private static function allHostedDomains(): array
    {
        $file = self::domainCacheFile();
        if (is_file($file) && (time() - filemtime($file)) < self::LIST_CACHE_TTL) {
            $d = json_decode((string)file_get_contents($file), true);
            if (is_array($d) && $d) return $d;
        }

        $domains = self::queryDomains(null, false);
        if ($domains) {
            $domains = self::withWordPressSubsites($domains);
            // Never cache an empty list: that would hide every site for a day
            file_put_contents($file, json_encode($domains));
        }
        return $domains ?? [];
    }

    public static function invalidateCache(): void
    {
        self::$requestCache = null;
        @unlink(self::domainCacheFile());
        @unlink(self::wpCacheFile());
        @unlink(VtDataDir::path('vt_wp_sources.json'));
        @unlink(VtDataDir::path('vt_wp_dns.json'));
        @unlink(VtDataDir::path('vt_wpconfigs.json'));
    }

    // ── Private ─────────────────────────────────────────────────

    private static function fromPlesk(): array
    {
        $client = pm_Session::getClient();
        if (!$client) throw new \Exception('No Plesk session client');

        if ($client->isAdmin()) {
            return self::allHostedDomains();
        }

        // null = DB unavailable or login unknown: show nothing rather than everything
        $own = self::queryDomains($client->getLogin(), $client->isReseller());
        return $own ? self::withWordPressSubsites($own) : [];
    }

    /**
     * Hosted, active domains from the psa database.
     * $login null = all domains; otherwise only those owned by that login
     * (resellers also get their direct clients' domains).
     * Returns null on failure, or a sorted list (possibly empty).
     */
    private static function queryDomains(?string $login, bool $isReseller): ?array
    {
        $pdo = self::pleskPdo();
        if (!$pdo) return null;

        $sql    = "SELECT LOWER(d.name) FROM domains d WHERE d.htype = 'vrt_hst' AND d.status = 0";
        $params = [];
        try {
            if ($login !== null) {
                $stmt = $pdo->prepare('SELECT id FROM clients WHERE login = ? LIMIT 1');
                $stmt->execute([$login]);
                $clientId = $stmt->fetchColumn();
                if (!$clientId) return null;

                if ($isReseller) {
                    $sql   .= ' AND (d.cl_id = ? OR d.cl_id IN (SELECT id FROM clients WHERE parent_id = ?))';
                    $params = [$clientId, $clientId];
                } else {
                    $sql   .= ' AND d.cl_id = ?';
                    $params = [$clientId];
                }
            }
            $stmt = $pdo->prepare($sql . ' ORDER BY d.name');
            $stmt->execute($params);
            $rows = $stmt->fetchAll(\PDO::FETCH_COLUMN) ?: [];
        } catch (\PDOException $e) {
            return null;
        }

        $domains = [];
        foreach ($rows as $name) {
            $name = trim((string)$name);
            if (!preg_match('/^[a-z0-9.\-]+\.[a-z0-9\-]{2,}$/', $name)) continue;
            if (preg_match('/(^|\.)localhost$|\.local$|\.test$|\.invalid$/', $name)) continue;
            $domains[$name] = true;
        }
        $domains = array_keys($domains);
        sort($domains);
        return $domains;
    }

    /**
     * Add WordPress hostnames that fall under one of $hosted but are not in it.
     * WordPress databases of forwarded or unhosted domains are ignored.
     */
    private static function withWordPressSubsites(array $hosted): array
    {
        $set  = array_flip($hosted);
        $skip = self::inactiveDomains();
        $add  = [];
        foreach (self::wordPressHosts() as $host) {
            if (isset($set[$host]) || isset($skip[$host])) continue; // disabled/suspended/no hosting in Plesk
            $parts = explode('.', $host);
            for ($i = 1; $i < count($parts) - 1; $i++) {
                if (isset($set[implode('.', array_slice($parts, $i))])) { $add[] = $host; break; }
            }
        }
        // A leftover WordPress database (an old copy or test site) can still name a hostname
        // that no longer exists. Only keep hostnames that resolve in DNS: nobody can visit the rest.
        foreach (self::resolving($add) as $host) $set[$host] = true;
        $all = array_keys($set);
        sort($all);
        return $all;
    }

    /**
     * Related hostnames for the current user: named in a WordPress database under one of
     * the user's domains, but not in DNS (old copies, test or staging sites). VirusTotal
     * often still lists them, so they are shown separately and only checked on request.
     */
    public static function relatedForCurrentUser(): array
    {
        $set  = array_flip(self::forCurrentUser(false));
        $skip = self::inactiveDomains();
        $cand = [];
        foreach (self::wordPressHosts() as $host) {
            if (isset($set[$host]) || isset($skip[$host])) continue;
            $parts = explode('.', $host);
            for ($i = 1; $i < count($parts) - 1; $i++) {
                if (isset($set[implode('.', array_slice($parts, $i))])) { $cand[] = $host; break; }
            }
        }
        $ok  = array_flip(self::resolving($cand));
        $out = array_values(array_filter($cand, fn($h) => !isset($ok[$h])));
        sort($out);
        return $out;
    }

    /** The hostnames that have an A, AAAA or CNAME record (results cached for a day) */
    private static function resolving(array $hosts): array
    {
        if (!$hosts) return [];
        $file  = VtDataDir::path('vt_wp_dns.json');
        $cache = is_file($file) ? (json_decode((string)file_get_contents($file), true) ?: []) : [];
        $now   = time();
        $out   = [];
        $dirty = false;
        foreach ($hosts as $h) {
            if (!isset($cache[$h]) || $now - (int)($cache[$h]['t'] ?? 0) > self::DOMAIN_CACHE_TTL) {
                $r = @dns_get_record($h, DNS_A | DNS_AAAA | DNS_CNAME);
                $cache[$h] = ['ok' => !empty($r), 't' => $now];
                $dirty = true;
            }
            if ($cache[$h]['ok']) $out[] = $h;
        }
        if ($dirty) @file_put_contents($file, json_encode($cache));
        return $out;
    }

    /** Domains Plesk knows but that are disabled, suspended or have no hosting (as a set) */
    private static function inactiveDomains(): array
    {
        $pdo = self::pleskPdo();
        if (!$pdo) return [];
        try {
            $rows = $pdo->query("SELECT LOWER(name) FROM domains WHERE NOT (htype = 'vrt_hst' AND status = 0)")
                        ->fetchAll(\PDO::FETCH_COLUMN) ?: [];
        } catch (\PDOException $e) {
            return [];
        }
        return array_flip(array_map('trim', $rows));
    }

    /** Hostnames from the siteurl option of every WordPress database, cached for a day */
    private static function wordPressHosts(): array
    {
        $file = self::wpCacheFile();
        if (is_file($file) && (time() - filemtime($file)) < self::DOMAIN_CACHE_TTL) {
            $d = json_decode((string)file_get_contents($file), true);
            if (is_array($d)) return $d;
        }
        $src = self::wordPressSources(true);
        if ($src === null) return [];
        $hosts = array_values(array_unique(array_column($src, 'host')));
        file_put_contents($file, json_encode($hosts));
        return $hosts;
    }

    /**
     * Every WordPress install found in the databases: [host, db, prefix, tables, size],
     * cached for a day. size = bytes of the tables with that prefix (data + indexes).
     */
    public static function wordPressSources(bool $fresh = false): ?array
    {
        $file = VtDataDir::path('vt_wp_sources.json');
        if (!$fresh && is_file($file) && (time() - filemtime($file)) < self::DOMAIN_CACHE_TTL) {
            $d = json_decode((string)file_get_contents($file), true);
            if (is_array($d)) return $d;
        }
        $pdo = self::pleskPdo();
        if (!$pdo) return null;

        $skip = "('information_schema','mysql','performance_schema','sys','psa','apsc','horde','roundcube')";
        $out  = [];
        try {
            $tables = $pdo->query(
                "SELECT TABLE_SCHEMA, TABLE_NAME FROM information_schema.TABLES
                 WHERE TABLE_NAME LIKE '%options' AND TABLE_SCHEMA NOT IN $skip"
            )->fetchAll(\PDO::FETCH_NUM);
            foreach ($tables as [$db, $tbl]) {
                try {
                    $url = $pdo->query('SELECT option_value FROM `' . str_replace('`', '', $db) . '`.`'
                        . str_replace('`', '', $tbl) . "` WHERE option_name = 'siteurl' LIMIT 1")->fetchColumn();
                } catch (\PDOException $e) {
                    continue; // not a WordPress options table
                }
                $host = strtolower((string)parse_url(trim((string)$url), PHP_URL_HOST));
                $host = preg_replace('/^www\./', '', $host);
                if (!$host || !preg_match('/^[a-z0-9.\-]+\.[a-z0-9\-]{2,}$/', $host)) continue;
                $out[] = ['host' => $host, 'db' => $db, 'prefix' => substr($tbl, 0, -strlen('options')), 'tables' => 0, 'size' => 0];
            }
            // Table count and size per install; a table belongs to the longest prefix that matches
            $dbs = array_values(array_unique(array_column($out, 'db')));
            if ($dbs) {
                $in   = implode(',', array_map([$pdo, 'quote'], $dbs));
                $rows = $pdo->query("SELECT TABLE_SCHEMA, TABLE_NAME, COALESCE(DATA_LENGTH,0)+COALESCE(INDEX_LENGTH,0)
                                     FROM information_schema.TABLES WHERE TABLE_SCHEMA IN ($in)")->fetchAll(\PDO::FETCH_NUM);
                foreach ($rows as [$db, $tbl, $size]) {
                    $best = -1;
                    foreach ($out as $i => $s) {
                        if ($s['db'] === $db && strpos($tbl, $s['prefix']) === 0
                            && ($best < 0 || strlen($s['prefix']) > strlen($out[$best]['prefix']))) $best = $i;
                    }
                    if ($best >= 0) { $out[$best]['tables']++; $out[$best]['size'] += (int)$size; }
                }
            }
        } catch (\PDOException $e) {
            return null;
        }
        @file_put_contents($file, json_encode($out));
        return $out;
    }

    /**
     * Owner of each site, for grouping the list: [domain => [c: customer name, cl: customer login,
     * ct: admin|reseller|client, r: reseller name ('' = the administrator's own customers), rl: reseller login]].
     * Hostnames that are not a Plesk domain (WordPress subsites) take the owner of their parent domain.
     */
    public static function owners(array $domains): array
    {
        $pdo = self::pleskPdo();
        if (!$pdo || !$domains) return [];
        try {
            $rows = $pdo->query("SELECT LOWER(d.name), c.type, COALESCE(NULLIF(TRIM(c.cname),''), c.pname), c.login,
                                        p.type, COALESCE(NULLIF(TRIM(p.cname),''), p.pname), p.login
                                 FROM domains d JOIN clients c ON c.id = d.cl_id
                                 LEFT JOIN clients p ON p.id = c.parent_id")->fetchAll(\PDO::FETCH_NUM);
        } catch (\PDOException $e) {
            return [];
        }
        $byName = [];
        foreach ($rows as [$dn, $ct, $cn, $cl, $pt, $pn, $pl]) {
            if ($ct === 'reseller')      $o = ['c' => $cn, 'cl' => $cl, 'ct' => 'reseller', 'r' => $cn, 'rl' => $cl];   // the reseller's own sites
            elseif ($ct === 'admin')     $o = ['c' => 'Administrator', 'cl' => $cl, 'ct' => 'admin', 'r' => '', 'rl' => ''];
            elseif ($pt === 'reseller')  $o = ['c' => $cn, 'cl' => $cl, 'ct' => 'client', 'r' => $pn, 'rl' => $pl];
            else                         $o = ['c' => $cn, 'cl' => $cl, 'ct' => 'client', 'r' => '', 'rl' => ''];
            $byName[$dn] = $o;
        }
        $out = [];
        foreach ($domains as $d) {
            for ($n = $d; $n !== '' && substr_count($n, '.') >= 1; $n = substr($n, strpos($n, '.') + 1)) {
                if (isset($byName[$n])) { $out[$d] = $byName[$n]; break; }
            }
        }
        return $out;
    }

    /** Database id and the subscription (webspace) it belongs to, from Plesk: [db name => [id, webspace]] */
    public static function pleskDatabases(): array
    {
        $pdo = self::pleskPdo();
        if (!$pdo) return [];
        try {
            $rows = $pdo->query("SELECT b.name, b.id, COALESCE(NULLIF(d.webspace_id,0), d.id), LOWER(w.name)
                                 FROM data_bases b JOIN domains d ON d.id = b.dom_id
                                 JOIN domains w ON w.id = COALESCE(NULLIF(d.webspace_id,0), d.id)")->fetchAll(\PDO::FETCH_NUM);
        } catch (\PDOException $e) {
            return [];
        }
        $out = [];
        foreach ($rows as [$name, $id, $ws, $wsName]) $out[$name] = ['id' => (int)$id, 'webspace' => (int)$ws, 'webspace_name' => $wsName];
        return $out;
    }

    /** Hosted domains (plus WordPress subsites) owned by a login; null if unknown */
    public static function forLogin(string $login, bool $isReseller): ?array
    {
        $own = self::queryDomains($login, $isReseller);
        return $own === null ? null : ($own ? self::withWordPressSubsites($own) : []);
    }

    /** Connection to Plesk's psa database (null when unavailable) */
    public static function pdo(): ?\PDO
    {
        static $pdo = false;
        if ($pdo === false) $pdo = self::pleskPdo();
        return $pdo;
    }

    private static function pleskPdo(): ?\PDO
    {
        $passFile = '/etc/psa/.psa.shadow';
        if (!is_readable($passFile)) return null;
        try {
            return new \PDO(
                'mysql:host=localhost;dbname=psa;charset=utf8',
                'admin',
                trim((string)file_get_contents($passFile)),
                [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]
            );
        } catch (\PDOException $e) {
            return null;
        }
    }
}
