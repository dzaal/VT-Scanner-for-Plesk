<?php
require_once __DIR__ . '/VtDataDir.php';

/**
 * VtEvents — rolling log of per-domain status changes.
 *
 * Stored in vt_events.json as an array of:
 *   { "domain": "example.com", "from": "clean", "to": "infected", "ts": 1234567890 }
 *   to = 'gone': a site that was infected or suspicious left the site list (deleted, disabled, suspended);
 *   approx = true: the time is when the site was last seen, not when it left.
 *
 * Called from VtCache::set() on every scan result that is stored.
 * Keeps at most MAX_KEEP events, and no event older than MAX_DAYS days.
 */
class VtEvents
{
    private const FILE     = 'vt_events.json';
    private const MAX_KEEP = 500;
    private const MAX_DAYS = 90;

    // Only transitions between these statuses are worth recording
    private const TRACK = ['clean', 'infected', 'suspicious'];

    /**
     * Record a status change. Called with the old and new scan result.
     * Silently ignored when the status did not change or is not interesting.
     */
    public static function record(string $domain, ?array $old, array $new): void
    {
        $from = $old['status'] ?? null;
        $to   = $new['status'] ?? null;

        // Only real changes between known statuses; a first result or pending -> clean is not a change
        if (!in_array($to, self::TRACK, true) || !in_array($from, self::TRACK, true)) return;
        if ($from === $to) return;
        self::add([['domain' => $domain, 'from' => $from, 'to' => $to, 'ts' => time()]]);
    }

    /** An infected or suspicious site left the site list (deleted, disabled or suspended in Plesk) */
    public static function gone(string $domain, string $from): void
    {
        self::add([['domain' => $domain, 'from' => $from, 'to' => 'gone', 'ts' => time()]]);
    }

    /**
     * Add the status changes from before the feed existed, found in the scan logs (cron_*.log).
     * Changes already in the feed are skipped, so it is safe to run again. Returns how many were added.
     */
    public static function backfillFromLogs(): int
    {
        $map = ['clean' => 'clean', 'schoon' => 'clean', 'suspicious' => 'suspicious', 'verdacht' => 'suspicious', 'infected' => 'infected', 'besmet' => 'infected'];
        $ev  = [];
        foreach (glob(VtDataDir::path('cron_*.log')) ?: [] as $f) {
            foreach (@file($f) ?: [] as $l) {
                if (preg_match('/^\[([\d\- :]{19})\] (?:\[(?:manual|scan)\] )?(?:\[(?:ok|\?|!)\] )?(clean|schoon|suspicious|verdacht|infected|besmet)\s+([a-z0-9.\-]+\.[a-z]{2,})/i', $l, $m)) {
                    $ev[] = [strtotime($m[1]), strtolower($m[3]), $map[strtolower($m[2])]];
                }
            }
        }
        usort($ev, fn($a, $b) => $a[0] <=> $b[0]);
        $state = []; $seen = []; $new = [];
        foreach ($ev as [$ts, $d, $st]) {
            if (isset($state[$d]) && $state[$d] !== $st) $new[] = ['domain' => $d, 'from' => $state[$d], 'to' => $st, 'ts' => $ts];
            $state[$d] = $st; $seen[$d] = $ts;
        }
        // Infected or suspicious sites that are no longer in the site list, since the daily history started
        $h = json_decode((string)@file_get_contents(VtDataDir::path('vt_history.json')), true);
        $list = json_decode((string)@file_get_contents(VtDataDir::path('vt_domains.json')), true);
        $list = is_array($list) ? array_flip($list) : [];
        if (!empty($h['last']) && $list) {
            $real  = array_keys(array_filter($h['days'] ?? [], fn($v) => empty($v['demo'])));
            $start = $real ? strtotime(min($real)) : PHP_INT_MAX;
            foreach ($state as $d => $st) {
                if (!isset($h['last'][$d]) && !isset($list[$d]) && in_array($st, ['infected', 'suspicious'], true) && $seen[$d] >= $start)
                    $new[] = ['domain' => $d, 'from' => $st, 'to' => 'gone', 'ts' => $seen[$d], 'approx' => true];
            }
        }
        return self::add($new);
    }

    /** Add events, skipping ones already there (same site, same new status, same day); returns how many were added */
    private static function add(array $new): int
    {
        $fp = @fopen(VtDataDir::path(self::FILE . '.lock'), 'c');
        if ($fp) flock($fp, LOCK_EX);
        $events = self::load();
        $known  = [];
        foreach ($events as $e) $known[($e['domain'] ?? '') . '|' . ($e['to'] ?? '') . '|' . date('Y-m-d', $e['ts'] ?? 0)] = 1;
        $n = 0;
        foreach ($new as $e) {
            $k = $e['domain'] . '|' . $e['to'] . '|' . date('Y-m-d', $e['ts']);
            if (isset($known[$k])) continue;
            $known[$k] = 1; $events[] = $e; $n++;
        }
        usort($events, fn($a, $b) => ($a['ts'] ?? 0) <=> ($b['ts'] ?? 0));

        // Prune old entries
        $cutoff = time() - self::MAX_DAYS * 86400;
        $events = array_values(array_filter($events, fn($e) => ($e['ts'] ?? 0) >= $cutoff));
        if (count($events) > self::MAX_KEEP) {
            $events = array_slice($events, -self::MAX_KEEP);
        }

        if ($n) self::save($events);
        if ($fp) { flock($fp, LOCK_UN); fclose($fp); }
        return $n;
    }

    /** Return recent events, newest first. */
    public static function recent(int $limit = 200): array
    {
        // A first result (from pending or nothing) is not a change; older versions recorded those too
        $real = array_filter(self::load(), fn($e) => in_array($e['from'] ?? null, self::TRACK, true));
        return array_values(array_slice(array_reverse($real), 0, $limit));
    }

    private static function load(): array
    {
        $f = VtDataDir::path(self::FILE);
        if (!is_file($f)) return [];
        $d = json_decode((string)file_get_contents($f), true);
        return is_array($d) ? $d : [];
    }

    private static function save(array $events): void
    {
        $f = VtDataDir::path(self::FILE);
        if (@file_put_contents($f . '.tmp', json_encode($events)) !== false) @rename($f . '.tmp', $f);
    }
}
