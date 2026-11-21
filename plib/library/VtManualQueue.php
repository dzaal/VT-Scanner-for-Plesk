<?php
require_once __DIR__ . '/VtDataDir.php';

/**
 * VtManualQueue — server-side queue for manual rescans.
 *
 * Stored in vt_manual_queue.json as:
 *   { "pending": ["domain.com", ...], "queued_at": {"domain.com": 1234567890} }
 *
 * Flow:
 *   Browser  → POST enqueue-rescan   → VtManualQueue::enqueue()
 *   Browser  → POST dequeue-rescan   → VtManualQueue::dequeue()   (when in-browser completes first)
 *   Cron     → manual_scan.php       → peek() → process → dequeue()
 */
class VtManualQueue
{
    private static function file(): string
    {
        return VtDataDir::path('vt_manual_queue.json');
    }

    /** Add domains (skips duplicates already in queue). */
    public static function enqueue(array $domains): void
    {
        $q = self::load();
        foreach ($domains as $d) {
            if (!in_array($d, $q['pending'], true)) {
                $q['pending'][]      = $d;
                $q['queued_at'][$d]  = time();
            }
        }
        self::save($q);
    }

    /** Remove domains (called when the in-browser rescan finishes them before cron). */
    public static function dequeue(array $domains): void
    {
        $q = self::load();
        $q['pending'] = array_values(array_diff($q['pending'], $domains));
        foreach ($domains as $d) {
            unset($q['queued_at'][$d]);
        }
        self::save($q);
    }

    /**
     * Mark domains as processed by cron: removes from pending and records them
     * in a 'completed' list (retained for 2 hours so the UI can confirm it ran).
     */
    public static function complete(array $domains): void
    {
        $q = self::load();
        $q['pending'] = array_values(array_diff($q['pending'], $domains));
        $now = time();
        foreach ($domains as $d) {
            unset($q['queued_at'][$d]);
            if (!in_array($d, $q['completed'], true)) {
                $q['completed'][]      = $d;
                $q['completed_at'][$d] = $now;
            }
        }
        // Prune completed entries older than 2 hours
        $cutoff = $now - 7200;
        $q['completed'] = array_values(array_filter(
            $q['completed'], fn($d) => ($q['completed_at'][$d] ?? 0) > $cutoff
        ));
        foreach (array_keys($q['completed_at']) as $d) {
            if (($q['completed_at'][$d] ?? 0) <= $cutoff) unset($q['completed_at'][$d]);
        }
        self::save($q);
    }

    /** Return the full pending list (cron reads this). */
    public static function pending(): array
    {
        return self::load()['pending'];
    }

    /** Return the full queue state (for the UI status endpoint). */
    public static function all(): array
    {
        return self::load();
    }

    /** True when there is nothing left to process. */
    public static function isEmpty(): bool
    {
        return empty(self::load()['pending']);
    }

    // ── Helpers ─────────────────────────────────────────────────

    private static function load(): array
    {
        $f = self::file();
        $empty = ['pending' => [], 'queued_at' => [], 'completed' => [], 'completed_at' => []];
        if (!is_file($f)) return $empty;
        $d = json_decode((string)file_get_contents($f), true);
        return is_array($d)
            ? ($d + ['pending' => [], 'queued_at' => [], 'completed' => [], 'completed_at' => []])
            : $empty;
    }

    private static function save(array $q): void
    {
        file_put_contents(self::file(), json_encode($q));
    }
}
