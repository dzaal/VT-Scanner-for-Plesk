<?php
require_once __DIR__ . '/VtDataDir.php';
require_once __DIR__ . '/VtSettings.php';
require_once __DIR__ . '/VtSiteLog.php';
require_once __DIR__ . '/VtCheckRateLimiter.php';

/**
 * VtSafeBrowsing — Google Safe Browsing (Lookup API v4, threatMatches:find), the list Chrome,
 * Firefox and Safari use to warn visitors about unsafe sites.
 *
 * Official API with the administrator's own key (Google Cloud → APIs & Services → Safe Browsing API).
 * Google allows it for non-commercial use only. Only the site addresses are sent (https:// and
 * http:// of each site); Google may use them to improve its services.
 *
 * One request checks up to 500 addresses, so the nightly run checks every site in one or two
 * requests. Live checks (details opened, rescan) reuse a result for FRESH seconds: Google's terms
 * do not allow treating a site as unsafe on information older than 30 minutes, so an older result
 * is shown as "last known" and checked again.
 *
 * Stored in vt_gsb.json { domain: {checked_at, state: ok|failed, threats: [label], types: [TYPE], http, error?} }
 * Calls per day in vt_gsb_state.json { day, calls, not_before }.
 */
class VtSafeBrowsing
{
    const NAME     = 'Google Safe Browsing';
    const ENDPOINT = 'https://safebrowsing.googleapis.com/v4/threatMatches:find';
    const TYPES    = ['MALWARE' => 'Malware', 'SOCIAL_ENGINEERING' => 'Phishing', 'UNWANTED_SOFTWARE' => 'Unwanted software',
                      'POTENTIALLY_HARMFUL_APPLICATION' => 'Harmful app'];
    const PER_REQUEST = 250;  // sites per request (2 addresses each; Google's maximum is 500)
    const FRESH       = 1800; // seconds a result may be used (Google's 30-minute rule)
    const REVIEW_URL  = 'https://search.google.com/search-console/security-issues';
    const TEST_URL    = 'https://testsafebrowsing.appspot.com/s/phishing.html'; // Google's own test page, always listed

    // ── Settings (administrator, server-wide) ──────────────────

    public static function key(): string
    {
        return trim((string)(new VtSettings('admin'))->get('gsb_api_key', ''));
    }

    /** Switched on and a key stored */
    public static function enabled(): bool
    {
        return (bool)(new VtSettings('admin'))->get('gsb_enabled', false) && self::key() !== '';
    }

    /** "…abcd" for the Settings page; never the whole key */
    public static function maskedKey(): string
    {
        $k = self::key();
        return $k === '' ? '' : '…' . substr($k, -4);
    }

    // ── Results ────────────────────────────────────────────────

    /** [domain => result] for these sites */
    public static function results(array $domains): array
    {
        return array_intersect_key(self::load(), array_flip($domains));
    }

    /** Check one site now (or reuse a result younger than FRESH unless $force) */
    public static function live(string $domain, bool $force = false): array
    {
        $prev = self::load()[$domain] ?? null;
        if (!$force && $prev && ($prev['state'] ?? '') === 'ok' && time() - (int)($prev['checked_at'] ?? 0) < self::FRESH) {
            return $prev + ['reused' => true];
        }
        $wait = VtCheckRateLimiter::reserve('google');
        if ($wait) return ['state' => 'rate_limited', 'retry_after' => $wait];
        return self::checkAndStore([$domain])[$domain];
    }

    /** Nightly: every site, PER_REQUEST at a time. Returns how many sites Google lists. */
    public static function checkAll(array $domains): int
    {
        $listed = 0;
        foreach (array_chunk(array_values(array_unique($domains)), self::PER_REQUEST) as $chunk) {
            while (($wait = VtCheckRateLimiter::reserve('google')) > 0) sleep(min($wait, 60));
            foreach (self::checkAndStore($chunk) as $r) if (!empty($r['threats'])) $listed++;
        }
        return $listed;
    }

    /** Check with Google, save, log, and note changes in the site history */
    private static function checkAndStore(array $domains): array
    {
        $res  = self::lookup($domains);
        $prev = self::load();
        self::locked(function () use ($res) {
            $all = self::load();
            foreach ($res as $d => $r) $all[$d] = $r;
            self::save($all);
        });
        $failed = array_filter($res, fn($r) => $r['state'] !== 'ok');
        if ($failed) self::log('check failed for ' . count($failed) . ' site(s): ' . (reset($failed)['error'] ?? 'unknown'));
        foreach ($res as $d => $r) {
            if ($r['state'] !== 'ok') continue;
            if ($r['threats']) self::log('listed ' . $d . ': ' . implode(', ', $r['threats']));
            // History and status feed: only when Google lists a site, or stops listing it (not every clean check)
            $was = !empty($prev[$d]['threats']);
            if (!$r['threats'] && !$was) continue;
            VtSiteLog::avastResult($d, self::NAME, implode(', ', $r['threats']), implode(', ', $r['types']), (int)$r['checked_at']);
        }
        return $res;
    }

    // ── Google API ─────────────────────────────────────────────

    /**
     * Ask Google about these sites (at most PER_REQUEST). Returns
     * [domain => [checked_at, state: ok|failed, threats: [label], types: [TYPE], http, error?]]
     */
    public static function lookup(array $domains, ?string $key = null): array
    {
        $key = $key ?? self::key();
        $now = time();
        $out = [];
        foreach ($domains as $d) $out[$d] = ['checked_at' => $now, 'state' => 'failed', 'threats' => [], 'types' => [], 'http' => 0];
        if ($key === '') return array_map(fn($r) => $r + ['error' => 'No API key'], $out);

        $entries = [];
        foreach ($domains as $d) { $entries[] = ['url' => "https://$d/"]; $entries[] = ['url' => "http://$d/"]; }
        [$code, $body, $err] = self::post($key, $entries);
        $data = json_decode((string)$body, true);
        if ($err === '' && $code !== 200) $err = self::errorText($code, $data);
        if ($err === '' && !is_array($data)) $err = 'Invalid response';
        if ($err !== '') return array_map(fn($r) => ['http' => $code, 'error' => $err] + $r, $out);

        foreach ($data['matches'] ?? [] as $m) {
            $host = strtolower((string)parse_url((string)($m['threat']['url'] ?? ''), PHP_URL_HOST));
            $type = (string)($m['threatType'] ?? '');
            if (!isset($out[$host])) continue;
            if (!in_array($type, $out[$host]['types'], true)) $out[$host]['types'][] = $type;
        }
        foreach ($out as $d => &$r) {
            $r['state'] = 'ok'; $r['http'] = $code;
            $r['threats'] = array_values(array_unique(array_map(fn($t) => self::TYPES[$t] ?? $t, $r['types'])));
        }
        return $out;
    }

    /** Check a key with Google's own test page (always listed as phishing). [ok, message] */
    public static function testKey(string $key): array
    {
        [$code, $body, $err] = self::post($key, [['url' => self::TEST_URL]]);
        $data = json_decode((string)$body, true);
        if ($err !== '') return [false, $err];
        if ($code !== 200) return [false, self::errorText($code, $data)];
        $hit = !empty($data['matches']);
        return [$hit, $hit ? 'Key works: Google answered and listed its own test page as phishing.'
                           : 'Google answered, but did not list its own test page. Check the key restrictions.'];
    }

    /** How the last call to Google went: [ok: bool|null (null = no call yet), error, at] */
    public static function lastStatus(): array
    {
        $st = json_decode((string)@file_get_contents(VtDataDir::path('vt_gsb_state.json')), true) ?: [];
        if (empty($st['last_at'])) return ['ok' => null, 'error' => '', 'at' => 0];
        return ['ok' => (int)$st['last_code'] === 200, 'error' => (string)($st['last_error'] ?? ''), 'at' => (int)$st['last_at']];
    }

    /** Requests sent today */
    public static function callsToday(): int
    {
        $st = json_decode((string)@file_get_contents(VtDataDir::path('vt_gsb_state.json')), true) ?: [];
        return ($st['day'] ?? '') === date('Y-m-d') ? (int)($st['calls'] ?? 0) : 0;
    }

    private static function post(string $key, array $entries): array
    {
        $stateFile = VtDataDir::path('vt_gsb_state.json');
        $fp = @fopen($stateFile . '.lock', 'c');
        if ($fp) flock($fp, LOCK_EX);
        try {
            $st = json_decode((string)@file_get_contents($stateFile), true) ?: [];
            if (($st['not_before'] ?? 0) > time()) return [0, '', 'Paused by Google (quota or rate limit) until ' . date('H:i', (int)$st['not_before'])];
            if (($st['day'] ?? '') !== date('Y-m-d')) $st = ['day' => date('Y-m-d'), 'calls' => 0];
            $st['calls'] = (int)($st['calls'] ?? 0) + 1;
            @file_put_contents($stateFile, json_encode($st));
        } finally {
            if ($fp) { flock($fp, LOCK_UN); fclose($fp); }
        }

        $payload = json_encode([
            'client'     => ['clientId' => 'stingyscan-plesk', 'clientVersion' => '1'],
            'threatInfo' => ['threatTypes' => array_keys(self::TYPES), 'platformTypes' => ['ANY_PLATFORM'],
                             'threatEntryTypes' => ['URL'], 'threatEntries' => $entries],
        ]);
        $ch = curl_init(self::ENDPOINT . '?key=' . rawurlencode($key));
        curl_setopt_array($ch, [
            CURLOPT_POST => true, CURLOPT_POSTFIELDS => $payload, CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_CONNECTTIMEOUT => 5, CURLOPT_TIMEOUT => 20,
        ]);
        $body = curl_exec($ch);
        $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err  = $body === false ? 'Connection failed: ' . curl_error($ch) : '';
        curl_close($ch);
        // Remember how the last call went (for the green check / red cross in Settings)
        $fp = @fopen($stateFile . '.lock', 'c');
        if ($fp) flock($fp, LOCK_EX);
        $st = json_decode((string)@file_get_contents($stateFile), true) ?: [];
        if ($code === 429) $st['not_before'] = time() + 600; // quota or rate limit: pause all requests for 10 minutes
        $st['last_at'] = time(); $st['last_code'] = $code;
        $st['last_error'] = $code === 200 ? '' : ($err !== '' ? $err : self::errorText($code, json_decode((string)$body, true)));
        @file_put_contents($stateFile, json_encode($st));
        if ($fp) { flock($fp, LOCK_UN); fclose($fp); }
        return [$code, $body, $err];
    }

    private static function errorText(int $code, $data): string
    {
        $msg = is_array($data) ? (string)($data['error']['message'] ?? '') : '';
        if ($code === 400 && stripos($msg, 'API key') !== false) return 'API key not valid';
        if ($code === 403) return 'Access denied: is the Safe Browsing API enabled for this key, and is this server allowed? ' . $msg;
        if ($code === 429) return 'Quota or rate limit reached; paused for 10 minutes';
        return 'HTTP ' . $code . ($msg !== '' ? ': ' . $msg : '');
    }

    // ── Storage ────────────────────────────────────────────────

    private static function log(string $m): void
    {
        @file_put_contents(VtDataDir::path('cron_admin.log'), '[' . date('Y-m-d H:i:s') . '] [Google Safe Browsing] ' . $m . PHP_EOL, FILE_APPEND);
    }

    private static function locked(callable $fn): void
    {
        $fp = @fopen(VtDataDir::path('vt_gsb.json.lock'), 'c');
        if ($fp) flock($fp, LOCK_EX);
        try { $fn(); } finally { if ($fp) { flock($fp, LOCK_UN); fclose($fp); } }
    }

    private static function load(): array
    {
        $d = json_decode((string)@file_get_contents(VtDataDir::path('vt_gsb.json')), true);
        return is_array($d) ? $d : [];
    }

    private static function save(array $data): void
    {
        $f = VtDataDir::path('vt_gsb.json');
        if (@file_put_contents($f . '.tmp', json_encode($data)) !== false) @rename($f . '.tmp', $f);
    }
}
