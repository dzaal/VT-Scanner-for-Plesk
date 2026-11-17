<?php
require_once __DIR__ . '/VtDataDir.php';

/**
 * VtRateLimiter — global per-API-key rate limit enforcer.
 *
 * VT free tier limits (confirmed by live testing 2026-11-16):
 *   4 requests / minute  → TooManyRequestsError (Retry-After ~60s)
 *   500 requests / day   → QuotaExceededError   (Retry-After = seconds to midnight UTC)
 *
 * Tracks all requests for a given API key across ALL users,
 * so shared keys (admin fallback) are never blown past their quota.
 *
 * Uses LOCK_EX on writes to stay safe under concurrent requests.
 */
class VtRateLimiter
{
    const MAX_PER_MIN = 4;
    const MAX_PER_DAY = 500;
    const WINDOW_SECS = 60;

    private string $stateFile;

    public function __construct(string $apiKey)
    {
        $hash            = substr(hash('sha256', $apiKey), 0, 16);
        $this->stateFile = VtDataDir::path('vt_quota_' . $hash . '.json');
    }

    /**
     * Check whether a request can be sent right now.
     * Returns true if allowed.
     * Returns ['quota_exceeded' => true, 'retry_after' => int] if daily quota gone.
     * Returns ['wait' => int] if per-minute limit is active (seconds to wait).
     */
    public function check(): bool|array
    {
        $state = $this->load();
        $now   = time();

        if ($state['date'] !== date('Y-m-d', $now)) {
            $state = $this->fresh($now);
        }

        // Daily quota check
        if ($state['day_count'] >= self::MAX_PER_DAY) {
            $midnight = strtotime('tomorrow 00:00:00 UTC');
            return ['quota_exceeded' => true, 'retry_after' => max(0, $midnight - $now)];
        }

        // Prune minute window to rolling 60s
        $cutoff              = $now - self::WINDOW_SECS;
        $state['minute_log'] = array_values(array_filter($state['minute_log'], fn($t) => $t > $cutoff));

        // Per-minute check
        if (count($state['minute_log']) >= self::MAX_PER_MIN) {
            $oldest = min($state['minute_log']);
            $wait   = (int)ceil(self::WINDOW_SECS - ($now - $oldest)) + 1;
            return ['wait' => max(1, $wait)];
        }

        return true;
    }

    /**
     * Record one outgoing request (call immediately after sending to VT).
     * Always call this regardless of VT's response — the request was sent
     * and counts against the quota.
     */
    public function record(): void
    {
        $lockFile = $this->stateFile . '.lock';
        $fp = fopen($lockFile, 'c');
        if (!$fp) return;
        flock($fp, LOCK_EX);

        $state = $this->load();
        $now   = time();

        if ($state['date'] !== date('Y-m-d', $now)) {
            $state = $this->fresh($now);
        }

        $cutoff                = $now - self::WINDOW_SECS;
        $state['minute_log']   = array_values(array_filter($state['minute_log'], fn($t) => $t > $cutoff));
        $state['minute_log'][] = $now;
        $state['day_count']++;

        $this->save($state);
        flock($fp, LOCK_UN);
        fclose($fp);
    }

    /** Requests used today */
    public function todayCount(): int
    {
        $state = $this->load();
        return ($state['date'] === date('Y-m-d')) ? (int)$state['day_count'] : 0;
    }

    /** Requests remaining today */
    public function remaining(): int
    {
        return max(0, self::MAX_PER_DAY - $this->todayCount());
    }

    /** Static helper — requests used today for a given key */
    public static function todayCountForKey(string $apiKey): int
    {
        if (!$apiKey) return 0;
        return (new self($apiKey))->todayCount();
    }

    // ── Private ─────────────────────────────────────────────────

    private function fresh(int $now): array
    {
        return ['date' => date('Y-m-d', $now), 'day_count' => 0, 'minute_log' => []];
    }

    private function load(): array
    {
        if (!file_exists($this->stateFile)) return $this->fresh(time());
        $data = json_decode(@file_get_contents($this->stateFile), true);
        if (!is_array($data) || !isset($data['date'], $data['day_count'], $data['minute_log'])) {
            return $this->fresh(time());
        }
        return $data;
    }

    private function save(array $state): void
    {
        file_put_contents($this->stateFile, json_encode($state), LOCK_EX);
    }
}
