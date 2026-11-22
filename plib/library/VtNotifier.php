<?php
require_once __DIR__ . '/VtDataDir.php';
require_once __DIR__ . '/VtSettings.php';
require_once __DIR__ . '/VtCache.php';
require_once __DIR__ . '/PleskDomains.php';

/**
 * VtNotifier: one summary e-mail with status changes and the full list of infected sites.
 *
 * Every Plesk user (admin, reseller, client) can switch notifications on in
 * Settings and enter one e-mail address. When one of their sites changes status,
 * the change is collected instead of mailed straight away. The collected changes
 * go out as one summary e-mail, together with the complete list of their sites
 * that are infected right now:
 *   - at the end of the nightly scan, and
 *   - during the day, once the oldest collected change is 10 minutes old
 *     (checked whenever the dashboard looks up or rescans a site).
 */
class VtNotifier
{
    const STATUSES  = ['clean', 'suspicious', 'infected'];
    const EVENTS    = ['infected', 'suspicious', 'clean'];
    const DAY_DELAY = 600; // seconds a daytime change waits, so several changes share one e-mail

    /**
     * Collect a status change for every user who wants to hear about it.
     * Returns the logins it was queued for.
     */
    public static function statusChanged(string $domain, ?array $old, array $new): array
    {
        $from = $old['status'] ?? null;
        $to   = $new['status'] ?? null;
        if (!in_array($to, self::STATUSES, true)) return [];      // errors, queued: no news yet
        if ($from === $to) return [];
        // "Clean" only counts as news when a flagged site recovers, not on a first scan
        if ($to === 'clean' && !in_array($from, ['suspicious', 'infected'], true)) return [];

        $queued = [];
        foreach (self::subscribers() as $sub) {
            if (!in_array($to, (array)$sub['notify_events'], true)) continue;
            if (!in_array($domain, self::domainsOf($sub), true)) continue;
            $q = self::loadQueue($sub['login']);
            $q[$domain] = [   // the latest change per site wins
                'domain' => $domain, 'from' => $q[$domain]['from'] ?? $from, 'to' => $to,
                'malicious' => $new['malicious'] ?? null, 'total' => $new['total'] ?? null,
                'flagged_by' => array_slice((array)($new['flagged_by'] ?? []), 0, 6),
                'at' => $q[$domain]['at'] ?? time(),
            ];
            // A site that went back to where it started is no news
            if ($q[$domain]['from'] === $to) unset($q[$domain]);
            self::saveQueue($sub['login'], $q);
            $queued[] = $sub['login'];
        }
        return $queued;
    }

    /**
     * Send the summary e-mails that are due ($force: all of them, e.g. after the nightly scan).
     * Returns the addresses that were e-mailed.
     */
    public static function flush(bool $force = false): array
    {
        $sent = [];
        foreach (self::subscribers() as $sub) {
            $q = self::loadQueue($sub['login']);
            if (!$q) continue;
            $oldest = min(array_column($q, 'at'));
            if (!$force && time() - $oldest < self::DAY_DELAY) continue;
            [$subject, $body] = self::digest($sub, array_values($q));
            if (self::send($sub['email'], $subject, $body)) {
                self::saveQueue($sub['login'], []);
                $sent[] = $sub['email'];
            }
        }
        return $sent;
    }

    /** Send a sample summary (current infected list) to the user's address. Returns it or null. */
    public static function sendTest(string $login, string $email): ?string
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) return null;
        $sub = ['login' => $login, 'role' => $login === 'admin' ? 'admin' : self::roleOf($login), 'email' => $email];
        [$subject, $body] = self::digest($sub, [], true);
        return self::send($email, $subject, $body) ? $email : null;
    }

    /** E-mail address of a Plesk login (the admin account included) */
    public static function emailOfLogin(string $login): ?string
    {
        $pdo = PleskDomains::pdo();
        if (!$pdo) return null;
        try {
            if ($login === 'admin') {
                $st = $pdo->query("SELECT email FROM clients WHERE type = 'admin' ORDER BY id LIMIT 1");
            } else {
                $st = $pdo->prepare('SELECT email FROM clients WHERE login = ? LIMIT 1');
                $st->execute([$login]);
            }
            $e = trim((string)$st->fetchColumn());
            return filter_var($e, FILTER_VALIDATE_EMAIL) ? $e : null;
        } catch (\PDOException $e) {
            return null;
        }
    }

    // ── Private ─────────────────────────────────────────────────

    /** Users with notifications on and a valid address, from their settings files */
    private static function subscribers(): array
    {
        static $subs = null;
        if ($subs !== null) return $subs;
        $subs = [];
        foreach (glob(VtDataDir::path('vt_settings_*.json')) ?: [] as $file) {
            $d = json_decode((string)@file_get_contents($file), true);
            if (!is_array($d) || empty($d['notify_enabled'])) continue;
            $login = (string)($d['login'] ?? '');
            if ($login === '' && basename($file) === 'vt_settings_admin.json') $login = 'admin';
            if ($login === '') continue;
            $email = trim((string)($d['notify_email'] ?? '')) ?: self::emailOfLogin($login);
            if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) continue;
            $role = $login === 'admin' ? 'admin' : self::roleOf($login);
            if (!$role) continue;
            $subs[] = array_merge($d, ['login' => $login, 'role' => $role, 'email' => $email]);
        }
        return $subs;
    }

    private static function roleOf(string $login): ?string
    {
        $pdo = PleskDomains::pdo();
        if (!$pdo) return null;
        try {
            $st = $pdo->prepare('SELECT type FROM clients WHERE login = ? LIMIT 1');
            $st->execute([$login]);
            $t = $st->fetchColumn();
        } catch (\PDOException $e) {
            return null;
        }
        if ($t === 'admin') return 'admin';
        if ($t === 'reseller') return 'reseller';
        return $t ? 'client' : null;
    }

    /** The sites this user sees in the scanner */
    private static function domainsOf(array $sub): array
    {
        static $lists = [];
        if (!isset($lists[$sub['login']])) {
            $wpOnly = (bool)(new VtSettings($sub['login']))->get('wordpress_only', false);
            $all = $sub['role'] === 'admin'
                ? PleskDomains::allDomains()
                : (PleskDomains::forLogin($sub['login'], $sub['role'] === 'reseller') ?? []);
            $lists[$sub['login']] = $wpOnly ? PleskDomains::onlyWordPress($all) : $all;
        }
        return $lists[$sub['login']];
    }

    private static function queueFile(string $login): string
    {
        return VtDataDir::path('vt_notify_' . preg_replace('/[^a-z0-9_\-]/i', '_', $login) . '.json');
    }

    private static function loadQueue(string $login): array
    {
        $d = json_decode((string)@file_get_contents(self::queueFile($login)), true);
        return is_array($d) ? $d : [];
    }

    private static function saveQueue(string $login, array $q): void
    {
        file_put_contents(self::queueFile($login), json_encode($q, JSON_PRETTY_PRINT));
    }

    private static function score(array $r): string
    {
        return isset($r['malicious'], $r['total']) ? $r['malicious'] . '/' . $r['total'] : '-';
    }

    /** Subject and body of a summary e-mail */
    private static function digest(array $sub, array $changes, bool $test = false): array
    {
        $results  = (new VtCache(new VtSettings('admin')))->all();
        $mine     = array_flip(self::domainsOf($sub));
        $infected = $suspicious = [];
        foreach ($results as $d => $r) {
            if (!isset($mine[$d])) continue;
            if (($r['status'] ?? '') === 'infected')   $infected[$d] = $r;
            if (($r['status'] ?? '') === 'suspicious') $suspicious[$d] = $r;
        }
        uasort($infected, fn($a, $b) => ($b['malicious'] ?? 0) <=> ($a['malicious'] ?? 0));
        ksort($suspicious);

        $n = count($changes);
        $subject = '[StingyScan] '
            . ($test ? 'Test: ' : '')
            . ($n ? $n . ' status change' . ($n === 1 ? '' : 's') . ', ' : '')
            . count($infected) . ' infected site' . (count($infected) === 1 ? '' : 's');

        $L = [];
        if ($test) {
            $L[] = 'This is a test of your StingyScan notifications. Real e-mails look';
            $L[] = 'like this, with the status changes at the top.';
            $L[] = '';
        }
        if ($changes) {
            $L[] = 'STATUS CHANGES';
            $L[] = str_repeat('-', 60);
            usort($changes, fn($a, $b) => array_search($a['to'], ['infected', 'suspicious', 'clean']) <=> array_search($b['to'], ['infected', 'suspicious', 'clean']));
            foreach ($changes as $c) {
                $L[] = sprintf('%-34s %s -> %s  (%s)', $c['domain'], ucfirst($c['from'] ?: 'new'), strtoupper($c['to']), self::score($c));
                if (!empty($c['flagged_by']) && $c['to'] !== 'clean') $L[] = '    flagged by: ' . implode(', ', $c['flagged_by']);
            }
            $L[] = '';
        }
        $L[] = 'INFECTED NOW (' . count($infected) . ')';
        $L[] = str_repeat('-', 60);
        if ($infected) {
            foreach ($infected as $d => $r) $L[] = sprintf('%-34s %s', $d, self::score($r));
        } else {
            $L[] = 'None.';
        }
        $L[] = '';
        if ($suspicious) {
            $L[] = 'Also suspicious (' . count($suspicious) . '): ' . implode(', ', array_keys($suspicious));
            $L[] = '';
        }
        $L[] = 'Infected or suspicious often means one or a few vendors flag the site, which';
        $L[] = 'can be a false alarm. Check the site, clean it if needed, then click Rescan.';
        $L[] = '';
        $L[] = 'StingyScan: ' . self::panelUrl();
        $L[] = '';
        $L[] = '-- ';
        $L[] = 'StingyScan on ' . self::host() . '. Change or switch off these e-mails in Settings.';
        return [$subject, implode("\n", $L) . "\n"];
    }

    private static function send(string $to, string $subject, string $body): bool
    {
        $from = self::emailOfLogin('admin') ?: ('noreply@' . self::host());
        $headers = implode("\r\n", [
            'From: StingyScan <' . $from . '>',
            'MIME-Version: 1.0',
            'Content-Type: text/plain; charset=UTF-8',
            'Content-Transfer-Encoding: 8bit',
            'X-Mailer: StingyScan',
        ]);
        return @mail($to, '=?UTF-8?B?' . base64_encode($subject) . '?=', $body, $headers);
    }

    private static function host(): string
    {
        return (string)(gethostname() ?: 'localhost');
    }

    private static function panelUrl(): string
    {
        return 'https://' . self::host() . ':8443/modules/stingyscan/';
    }
}
