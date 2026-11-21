<?php
require_once __DIR__ . '/VtDataDir.php';

/**
 * VtEvents — rolling log of per-domain status changes.
 *
 * Stored in vt_events.json as an array of:
 *   { "domain": "example.com", "from": "clean", "to": "infected", "ts": 1234567890 }
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

        if (!in_array($to, self::TRACK, true)) return;
        if ($from === $to) return;

        $events   = self::load();
        $events[] = ['domain' => $domain, 'from' => $from, 'to' => $to, 'ts' => time()];

        // Prune old entries
        $cutoff = time() - self::MAX_DAYS * 86400;
        $events = array_values(array_filter($events, fn($e) => ($e['ts'] ?? 0) >= $cutoff));
        if (count($events) > self::MAX_KEEP) {
            $events = array_slice($events, -self::MAX_KEEP);
        }

        self::save($events);
    }

    /** Return recent events, newest first. */
    public static function recent(int $limit = 200): array
    {
        return array_values(array_slice(array_reverse(self::load()), 0, $limit));
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
        file_put_contents(VtDataDir::path(self::FILE), json_encode($events));
    }
}
