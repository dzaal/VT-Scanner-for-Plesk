<?php
require_once __DIR__ . '/VtDataDir.php';

/**
 * VtManualQueue — server-side queue for manual rescans.
 *
 * Stored in vt_manual_queue.json as:
 *   { "pending": ["domain.com", ...], "queued_at": {"domain.com": ts}, "alive_at": {"domain.com": ts},
 *     "completed": [...], "completed_at": {...} }
 *
 * Flow:
 *   Browser  → POST enqueue-rescan   → enqueue()      (Rescan clicked; the browser also runs it itself)
 *   Browser  → POST rescan-alive     → touch()        (every 30 s while the page still has the rescan)
 *   Browser  → POST dequeue-rescan   → dequeue()      (the browser finished it)
 *   Cron     → manual_scan.php       → takeable() → process → complete()
 *
 * Cron only takes a domain when no open page has reported it for ORPHAN_AFTER seconds, so a rescan
 * is never run by the browser and by cron at the same time.
 *
 * Every change is made under one lock and written atomically (tmp file + rename), so the browser
 * and cron never overwrite each other's changes.
 */
class VtManualQueue
{
    /** Seconds without a heartbeat from the browser before cron takes the domain over */
    const ORPHAN_AFTER = 180;

    private static function file(): string
    {
        return VtDataDir::path('vt_manual_queue.json');
    }

    /** Add domains (skips duplicates already in queue). */
    public static function enqueue(array $domains): void
    {
        self::update(function (array $q) use ($domains) {
            $now = time();
            foreach ($domains as $d) {
                if (!in_array($d, $q['pending'], true)) {
                    $q['pending'][]     = $d;
                    $q['queued_at'][$d] = $now;
                }
                $q['alive_at'][$d] = $now;
            }
            return $q;
        });
    }

    /** An open page still has these rescans (running or waiting): cron must leave them alone. */
    public static function touch(array $domains): void
    {
        self::update(function (array $q) use ($domains) {
            $now = time();
            foreach ($domains as $d) {
                if (in_array($d, $q['pending'], true)) $q['alive_at'][$d] = $now;
            }
            return $q;
        });
    }

    /** Remove domains (called when the in-browser rescan finishes them before cron). */
    public static function dequeue(array $domains): void
    {
        self::update(function (array $q) use ($domains) {
            $q['pending'] = array_values(array_diff($q['pending'], $domains));
            foreach ($domains as $d) unset($q['queued_at'][$d], $q['alive_at'][$d]);
            return $q;
        });
    }

    /**
     * Mark domains as processed by cron: removes from pending and records them
     * in a 'completed' list (retained for 2 hours so the UI can confirm it ran).
     */
    public static function complete(array $domains): void
    {
        self::update(function (array $q) use ($domains) {
            $q['pending'] = array_values(array_diff($q['pending'], $domains));
            $now = time();
            foreach ($domains as $d) {
                unset($q['queued_at'][$d], $q['alive_at'][$d]);
                if (!in_array($d, $q['completed'], true)) $q['completed'][] = $d;
                $q['completed_at'][$d] = $now;
            }
            // Prune completed entries older than 2 hours
            $cutoff = $now - 7200;
            $q['completed'] = array_values(array_filter($q['completed'], fn($d) => ($q['completed_at'][$d] ?? 0) > $cutoff));
            foreach (array_keys($q['completed_at']) as $d) {
                if (($q['completed_at'][$d] ?? 0) <= $cutoff) unset($q['completed_at'][$d]);
            }
            return $q;
        });
    }

    /** The full pending list. */
    public static function pending(): array
    {
        return self::load()['pending'];
    }

    /** Pending domains that no open page has reported for ORPHAN_AFTER seconds: these are for cron. */
    public static function takeable(): array
    {
        $q = self::load();
        $cut = time() - self::ORPHAN_AFTER;
        return array_values(array_filter($q['pending'], fn($d) => max((int)($q['queued_at'][$d] ?? 0), (int)($q['alive_at'][$d] ?? 0)) < $cut));
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

    /** Read, change and write the queue under one exclusive lock */
    private static function update(callable $fn): void
    {
        $fp = @fopen(self::file() . '.lock', 'c');
        if ($fp) flock($fp, LOCK_EX);
        try {
            $q = $fn(self::load());
            $f = self::file();
            if (@file_put_contents($f . '.tmp', json_encode($q)) !== false) @rename($f . '.tmp', $f);
        } finally {
            if ($fp) { flock($fp, LOCK_UN); fclose($fp); }
        }
    }

    private static function load(): array
    {
        $empty = ['pending' => [], 'queued_at' => [], 'alive_at' => [], 'completed' => [], 'completed_at' => []];
        $d = json_decode((string)@file_get_contents(self::file()), true);
        if (!is_array($d)) return $empty;
        $d += $empty;
        foreach ($empty as $k => $_) if (!is_array($d[$k])) $d[$k] = [];
        return $d;
    }
}
