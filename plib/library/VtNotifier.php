<?php
require_once __DIR__ . '/VtDataDir.php';
require_once __DIR__ . '/VtSettings.php';
require_once __DIR__ . '/PleskDomains.php';

/**
 * VtNotifier: e-mails when a site's status changes (clean / suspicious / infected).
 *
 * Every Plesk user (admin, reseller, client) sets their own notifications in
 * Settings. When a site's status changes, each user who can see that site and
 * subscribed to that change gets one e-mail, sent to the addresses they chose:
 *   me       the user's own e-mail address in Plesk
 *   owner    the customer who owns the site       (admin and resellers only)
 *   reseller the reseller the site belongs to      (admin only)
 * Each address gets at most one e-mail per change, even if several users chose it.
 */
class VtNotifier
{
    const STATUSES = ['clean', 'suspicious', 'infected'];
    const EVENTS   = ['infected', 'suspicious', 'clean'];

    /** Recipient options a role may choose from */
    public static function allowedTargets(string $role): array
    {
        if ($role === 'admin')    return ['me', 'owner', 'reseller'];
        if ($role === 'reseller') return ['me', 'owner'];
        return ['me'];
    }

    /**
     * Called after a new result is stored. Returns the addresses that were e-mailed.
     */
    public static function statusChanged(string $domain, ?array $old, array $new): array
    {
        $from = $old['status'] ?? null;
        $to   = $new['status'] ?? null;
        if (!in_array($to, self::STATUSES, true)) return [];      // errors, queued: no news yet
        if ($from === $to) return [];
        // "Clean" only counts as news when a flagged site recovers, not on a first scan
        if ($to === 'clean' && !in_array($from, ['suspicious', 'infected'], true)) return [];

        $recipients = [];
        foreach (self::subscribers() as $sub) {
            if (!in_array($to, (array)$sub['notify_events'], true)) continue;
            if (!self::userCanSee($sub, $domain)) continue;
            foreach (self::addressesFor($sub, $domain) as $email) $recipients[strtolower($email)] = $email;
        }
        if (!$recipients) return [];

        [$subject, $body] = self::message($domain, $from, $new);
        $sent = [];
        foreach ($recipients as $email) {
            if (self::send($email, $subject, $body)) $sent[] = $email;
        }
        return $sent;
    }

    /** Send a test e-mail to the given user's own address. Returns the address or null. */
    public static function sendTest(string $login): ?string
    {
        $email = self::emailOfLogin($login);
        if (!$email) return null;
        $body = "This is a test message from VT Site Scanner on " . self::host() . ".\n\n"
              . "Notifications are working: you will get an e-mail like this when a site's\n"
              . "status changes to one of the statuses you selected in Settings.\n\n"
              . "Open the scanner: " . self::panelUrl() . "\n";
        return self::send($email, '[VT Site Scanner] Test notification', $body) ? $email : null;
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

    /** Users with notifications switched on, from their settings files */
    private static function subscribers(): array
    {
        $subs = [];
        foreach (glob(VtDataDir::path('vt_settings_*.json')) ?: [] as $file) {
            $d = json_decode((string)@file_get_contents($file), true);
            if (!is_array($d) || empty($d['notify_enabled'])) continue;
            $login = (string)($d['login'] ?? '');
            if ($login === '' && basename($file) === 'vt_settings_admin.json') $login = 'admin';
            if ($login === '') continue;
            $d['login'] = $login;
            $d['role']  = $login === 'admin' ? 'admin' : self::roleOf($login);
            if (!$d['role']) continue;
            $d['notify_to'] = array_values(array_intersect((array)($d['notify_to'] ?? []), self::allowedTargets($d['role'])));
            $subs[] = $d;
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

    private static function userCanSee(array $sub, string $domain): bool
    {
        if ($sub['role'] === 'admin') return true;
        static $lists = [];
        $lists[$sub['login']] ??= PleskDomains::forLogin($sub['login'], $sub['role'] === 'reseller') ?? [];
        return in_array($domain, $lists[$sub['login']], true);
    }

    private static function addressesFor(array $sub, string $domain): array
    {
        $out = [];
        foreach ($sub['notify_to'] as $target) {
            if ($target === 'me') {
                $e = self::emailOfLogin($sub['login']);
            } else {
                $owner = self::ownerOf($domain);
                $e = $target === 'owner' ? ($owner['email'] ?? null) : ($owner['reseller_email'] ?? null);
            }
            if ($e && filter_var($e, FILTER_VALIDATE_EMAIL)) $out[] = $e;
        }
        return $out;
    }

    /**
     * Owner of a site and the reseller above it. Sites on a hostname that is not
     * a Plesk domain itself (e.g. a WordPress subsite) belong to the parent domain.
     */
    private static function ownerOf(string $domain): array
    {
        static $cache = [];
        if (isset($cache[$domain])) return $cache[$domain];
        $pdo = PleskDomains::pdo();
        $res = [];
        if ($pdo) {
            $parts = explode('.', $domain);
            try {
                $st = $pdo->prepare(
                    'SELECT c.email, c.type, p.email AS parent_email, p.type AS parent_type
                     FROM domains d JOIN clients c ON c.id = d.cl_id LEFT JOIN clients p ON p.id = c.parent_id
                     WHERE d.name = ? LIMIT 1');
                for ($i = 0; $i < count($parts) - 1 && !$res; $i++) {
                    $st->execute([implode('.', array_slice($parts, $i))]);
                    $row = $st->fetch(\PDO::FETCH_ASSOC);
                    if (!$row) continue;
                    // A reseller's own subscription: the reseller is both owner and reseller
                    $res = [
                        'email'          => $row['type'] === 'admin' ? null : $row['email'],
                        'reseller_email' => $row['type'] === 'reseller' ? $row['email']
                                          : ($row['parent_type'] === 'reseller' ? $row['parent_email'] : null),
                    ];
                }
            } catch (\PDOException $e) {
            }
        }
        return $cache[$domain] = $res;
    }

    private static function message(string $domain, ?string $from, array $new): array
    {
        $to    = $new['status'];
        $label = ['infected' => 'INFECTED', 'suspicious' => 'SUSPICIOUS', 'clean' => 'CLEAN again'][$to];
        $score = isset($new['malicious'], $new['total']) ? "{$new['malicious']}/{$new['total']}" : '';
        $subject = "[VT Site Scanner] $domain is $label" . ($score && $to !== 'clean' ? " ($score)" : '');

        $lines = [
            "The VirusTotal status of $domain has changed.",
            '',
            'Site:       ' . $domain,
            'Status:     ' . ucfirst($from ?: 'not checked yet') . ' -> ' . ucfirst($to),
        ];
        if ($score) $lines[] = 'Detections: ' . $score . ' security vendors flag this site';
        if (!empty($new['flagged_by'])) $lines[] = 'Flagged by: ' . implode(', ', array_slice((array)$new['flagged_by'], 0, 8));
        $lines[] = '';
        if ($to === 'infected') {
            $lines[] = 'Check the site for malware or phishing pages, clean it up, and then click';
            $lines[] = 'Rescan in VT Site Scanner. If the site is clean, ask the vendors above to';
            $lines[] = 'review it as a false positive.';
        } elseif ($to === 'suspicious') {
            $lines[] = 'A few vendors flag this site. Check it, and click Rescan once it is cleaned up.';
        } else {
            $lines[] = 'No vendor flags this site anymore.';
        }
        $lines[] = '';
        $lines[] = 'VirusTotal report: https://www.virustotal.com/gui/domain/' . rawurlencode($domain);
        $lines[] = 'VT Site Scanner:   ' . self::panelUrl();
        $lines[] = '';
        $lines[] = '-- ';
        $lines[] = 'VT Site Scanner on ' . self::host() . '. Change or switch off these e-mails in Settings.';
        return [$subject, implode("\n", $lines) . "\n"];
    }

    private static function send(string $to, string $subject, string $body): bool
    {
        $from = self::emailOfLogin('admin') ?: ('noreply@' . self::host());
        $headers = implode("\r\n", [
            'From: VT Site Scanner <' . $from . '>',
            'MIME-Version: 1.0',
            'Content-Type: text/plain; charset=UTF-8',
            'Content-Transfer-Encoding: 8bit',
            'X-Mailer: VT Site Scanner',
        ]);
        return @mail($to, '=?UTF-8?B?' . base64_encode($subject) . '?=', $body, $headers);
    }

    private static function host(): string
    {
        return (string)(gethostname() ?: 'localhost');
    }

    private static function panelUrl(): string
    {
        return 'https://' . self::host() . ':8443/modules/virustotal-extension/';
    }
}
