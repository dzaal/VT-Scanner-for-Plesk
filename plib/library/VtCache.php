<?php
require_once __DIR__ . '/VtDataDir.php';
require_once __DIR__ . '/VtSettings.php';
require_once __DIR__ . '/VtNotifier.php';
require_once __DIR__ . '/VtEvents.php';
require_once __DIR__ . '/VtSiteLog.php';

/**
 * VtCache — persistent cache for VirusTotal scan results.
 *
 * API budget strategy (free tier: 500 req/day, 4 req/min):
 *   - Results cached for CLEAN_TTL or INFECTED_TTL (from VtSettings)
 *   - Daily background scan picks up where it left off (persistent queue position)
 *   - auto_scan_share of daily_limit reserved for background scanning
 *   - Remaining fraction kept for manual Reanalyze buttons
 *   - On-demand reads from cache; only calls VT when cache is stale
 */
class VtCache
{
    // Results are shared across users (no need to scan same domain twice)
    public static function resultsFile(): string { return VtDataDir::path('vt_results.json'); }

    // Budget/state is per-user (each user has their own API key and limit)
    public static function stateFile(string $userId = 'admin'): string
    {
        return VtDataDir::path('vt_state_' . preg_replace('/[^a-z0-9_\-]/i', '_', $userId) . '.json');
    }

    private array      $results  = [];
    private array      $state    = [];
    private VtSettings $settings;

    public function __construct(VtSettings $settings = null)
    {
        VtDataDir::ensureExists();
        $this->settings = $settings ?? new VtSettings();
        $this->results  = $this->loadJson(self::resultsFile());
        $this->state    = $this->loadJson(self::stateFile($this->settings->getUserId()));
        $this->rollDayIfNeeded();
    }

    // ── Read/write results ──────────────────────────────────────

    public function get(string $domain): ?array
    {
        return $this->results[$domain] ?? null;
    }

    /** Logins a status change was queued for by the last set() call (summary e-mail) */
    public array $lastNotified = [];

    public function set(string $domain, array $result, bool $manual = false): void
    {
        $old = $this->results[$domain] ?? null;
        $result['cached_at'] = time();
        $this->results[$domain] = $result;
        $this->saveJson(self::resultsFile(), $this->results);
        // Increment appropriate counter
        $key = $manual ? 'manual' : 'auto';
        $this->state['today'][$key] = ($this->state['today'][$key] ?? 0) + 1;
        $this->saveJson(self::stateFile($this->settings->getUserId()), $this->state);

        $this->lastNotified = [];
        try {
            $this->lastNotified = VtNotifier::statusChanged($domain, $old, $result);
        } catch (\Throwable $e) {
            // A mail problem must never break scanning
        }
        try {
            VtEvents::record($domain, $old, $result);
            VtSiteLog::recordScan($domain, $old, $result);
        } catch (\Throwable $e) {
        }
    }

    /** Charge one call to the auto or manual daily budget without touching results */
    public function charge(bool $manual): void
    {
        $key = $manual ? 'manual' : 'auto';
        $this->state['today'][$key] = ($this->state['today'][$key] ?? 0) + 1;
        $this->saveJson(self::stateFile($this->settings->getUserId()), $this->state);
    }

    public function invalidate(string $domain): void
    {
        unset($this->results[$domain]);
        $this->saveJson(self::resultsFile(), $this->results);
    }

    public function all(): array
    {
        return $this->results;
    }

    // ── Staleness ───────────────────────────────────────────────

    public function isStale(string $domain): bool
    {
        $entry = $this->results[$domain] ?? null;
        if (!$entry) return true;
        $status = $entry['status'] ?? '';
        if (in_array($status, ['error', 'ratelimit'])) return true; // always retry errors
        // "pending" is a transient state (VT still analysing) — re-check after 10 min
        if ($status === 'pending') return (time() - ($entry['cached_at'] ?? 0)) > 600;
        $ttl = in_array($status, ['infected', 'suspicious'])
            ? $this->settings->get('infected_ttl_days', 1)  * 86400
            : $this->settings->get('clean_ttl_days',    30) * 86400;
        return (time() - ($entry['cached_at'] ?? 0)) > $ttl;
    }

    /** Remove all cached error/ratelimit entries and reset today's auto counter */
    public function clearErrors(): void
    {
        $changed = false;
        foreach ($this->results as $domain => $entry) {
            if (in_array($entry['status'] ?? '', ['error', 'ratelimit'])) {
                unset($this->results[$domain]);
                $changed = true;
            }
        }
        if ($changed) $this->saveJson(self::resultsFile(), $this->results);
        // Also reset auto counter — those were all wasted on a bad API key
        $this->state['today']['auto'] = 0;
        $this->saveJson(self::stateFile($this->settings->getUserId()), $this->state);
    }

    // ── Background scan queue (continues where it left off) ─────

    /**
     * Returns up to $limit domains that need scanning today.
     * Continues from the position saved from yesterday's run.
     * Infected/suspicious domains are always at the front.
     */
    public function nextToScan(array $allDomains, int $limit): array
    {
        $budget = $this->autoBudgetRemaining();
        $limit  = min($limit, $budget);
        if ($limit <= 0) return [];

        // Always re-check infected/suspicious first (regardless of queue position)
        $urgent = [];
        $normal = [];
        foreach ($allDomains as $d) {
            if (!$this->isStale($d)) continue;
            $status = $this->results[$d]['status'] ?? 'unseen';
            if (in_array($status, ['infected', 'suspicious'])) {
                $urgent[] = $d;
            } else {
                $normal[] = $d;
            }
        }

        // For normal domains: resume from saved position (round-robin through all domains)
        $pos = $this->state['queue_pos'] ?? 0;
        $n   = count($normal);
        if ($n > 0) {
            $pos = $pos % $n;
            // Rotate list so we start at saved position
            $normal = array_merge(array_slice($normal, $pos), array_slice($normal, 0, $pos));
        }

        $queue = array_merge($urgent, $normal);
        $batch = array_slice($queue, 0, $limit);

        // Save new queue position (after urgent, advance normal position)
        $newPos = $pos + max(0, $limit - count($urgent));
        $this->state['queue_pos'] = $n > 0 ? ($newPos % $n) : 0;
        $this->saveJson(self::stateFile($this->settings->getUserId()), $this->state);

        return $batch;
    }

    // ── Budget ──────────────────────────────────────────────────

    public function autoBudgetRemaining(): int
    {
        return max(0, $this->settings->maxAutoPerDay() - ($this->state['today']['auto'] ?? 0));
    }

    public function manualBudgetRemaining(): int
    {
        return max(0, $this->settings->maxManualPerDay() - ($this->state['today']['manual'] ?? 0));
    }

    public function budgetInfo(): array
    {
        return [
            'auto_used'    => $this->state['today']['auto']   ?? 0,
            'manual_used'  => $this->state['today']['manual'] ?? 0,
            'auto_max'     => $this->settings->maxAutoPerDay(),
            'manual_max'   => $this->settings->maxManualPerDay(),
            'auto_left'    => $this->autoBudgetRemaining(),
            'manual_left'  => $this->manualBudgetRemaining(),
            'date'         => $this->state['date'] ?? date('Y-m-d'),
            'queue_pos'    => $this->state['queue_pos'] ?? 0,
        ];
    }

    // ── Helpers ─────────────────────────────────────────────────

    private function rollDayIfNeeded(): void
    {
        $today = date('Y-m-d');
        if (($this->state['date'] ?? '') !== $today) {
            // New day: reset daily counters, keep queue_pos
            $this->state = [
                'date'      => $today,
                'today'     => ['auto' => 0, 'manual' => 0],
                'queue_pos' => $this->state['queue_pos'] ?? 0,
            ];
            $this->saveJson(self::stateFile($this->settings->getUserId()), $this->state);
        }
    }

    private function loadJson(string $file): array
    {
        if (!file_exists($file)) return [];
        $d = json_decode(file_get_contents($file), true);
        return is_array($d) ? $d : [];
    }

    private function saveJson(string $file, array $data): void
    {
        file_put_contents($file, json_encode($data));
    }
}
