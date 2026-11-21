<?php
/**
 * Daily totals per scope: 'admin' = the whole server, 'r:<login>' = a reseller's customers and
 * own sites, 'c:<login>' = one customer's sites. Infected, suspicious and clean sites, plus how many
 * sites were cleaned (infected/suspicious -> clean) and newly infected that day.
 * Used for the trend graph. Stored in vt_history.json (server), vt_history_r_<login>.json (reseller)
 * or vt_history_c_<login>.json (customer):
 *   days: { "2026-11-17": {infected, suspicious, clean, total, cleaned, new_infected, demo?} }
 *   last: { domain: status }  (statuses at the previous snapshot, to count changes)
 *   changes: [ {t: "2026-11-20 11:10:22", d: domain, from, to} ]  (per-site status changes, oldest first;
 *            to = 'gone' when a site that was infected or suspicious left the site list)
 */
require_once __DIR__ . '/VtDataDir.php';

class VtHistory
{
    const KEEP_DAYS = 400;
    const KEEP_CHANGES = 5000;

    private static string $scope = 'admin';

    private static function file(): string
    {
        if (self::$scope === 'admin') return VtDataDir::path('vt_history.json');
        [$type, $login] = explode(':', self::$scope, 2) + [1 => ''];
        return VtDataDir::path('vt_history_' . ($type === 'c' ? 'c' : 'r') . '_' . preg_replace('/[^a-z0-9_\-]/i', '_', $login) . '.json');
    }

    /** All reseller and customer histories that exist: ['r:login', 'c:login', ...] */
    public static function scopes(): array
    {
        $out = [];
        foreach (glob(VtDataDir::path('vt_history_[rc]_*.json')) ?: [] as $f) {
            if (preg_match('/vt_history_([rc])_(.+)\.json$/', basename($f), $m)) $out[] = $m[1] . ':' . $m[2];
        }
        return $out;
    }

    /** Remove the demo days from the current history; returns how many were removed */
    public static function removeDemo(): int
    {
        $h = self::load();
        $before = count($h['days']);
        $h['days'] = array_filter($h['days'], fn($v) => empty($v['demo']));
        $n = $before - count($h['days']);
        if ($n) self::save($h);
        return $n;
    }

    /** Work on the history of 'admin' (the whole server), 'r:<login>' (a reseller) or 'c:<login>' (a customer) */
    public static function scope(string $scope): void { self::$scope = $scope !== '' ? $scope : 'admin'; }

    public static function load(): array
    {
        $d = json_decode((string)@file_get_contents(self::file()), true);
        return is_array($d) ? $d + ['days' => [], 'last' => [], 'changes' => []] : ['days' => [], 'last' => [], 'changes' => []];
    }

    private static function save(array $h): void
    {
        ksort($h['days']);
        $h['days'] = array_slice($h['days'], -self::KEEP_DAYS, null, true);
        usort($h['changes'], fn($a, $b) => strcmp($a['t'], $b['t']));
        $h['changes'] = array_slice($h['changes'], -self::KEEP_CHANGES);
        $tmp = self::file() . '.tmp';
        if (@file_put_contents($tmp, json_encode($h)) !== false) @rename($tmp, self::file());
    }

    /**
     * Record today's totals from the current status of every site ([domain => status]).
     * Cheap; safe to call on every dashboard load and from the nightly scan.
     */
    public static function record(array $statusByDomain): void
    {
        $fp = @fopen(self::file() . '.lock', 'c');
        if ($fp) flock($fp, LOCK_EX);
        $h     = self::load();
        $today = date('Y-m-d');
        $c     = ['infected' => 0, 'suspicious' => 0, 'clean' => 0];
        $cleaned = 0; $newInf = 0;
        foreach ($statusByDomain as $d => $st) {
            if (isset($c[$st])) $c[$st]++;
            $prev = $h['last'][$d] ?? null;
            if ($st === 'clean' && in_array($prev, ['infected', 'suspicious'], true)) $cleaned++;
            if ($st === 'infected' && $prev !== null && $prev !== 'infected') $newInf++;
            if (in_array($st, ['infected', 'suspicious', 'clean'], true)) {
                if ($prev !== null && $prev !== $st) $h['changes'][] = ['t' => date('Y-m-d H:i:s'), 'd' => $d, 'from' => $prev, 'to' => $st];
                $h['last'][$d] = $st;
            }
        }
        // Infected or suspicious sites that left the list (deleted, disabled or suspended in Plesk)
        foreach (array_diff_key($h['last'], $statusByDomain) as $d => $prev) {
            if (in_array($prev, ['infected', 'suspicious'], true)) $h['changes'][] = ['t' => date('Y-m-d H:i:s'), 'd' => $d, 'from' => $prev, 'to' => 'gone'];
        }
        $day = $h['days'][$today] ?? [];
        if (!empty($day['demo'])) $day = []; // real data replaces demo data
        $h['days'][$today] = $c + [
            'total'        => count($statusByDomain),
            'cleaned'      => (int)($day['cleaned'] ?? 0) + $cleaned,
            'new_infected' => (int)($day['new_infected'] ?? 0) + $newInf,
        ];
        // Forget sites that are gone
        $h['last'] = array_intersect_key($h['last'], $statusByDomain);
        self::save($h);
        if ($fp) { flock($fp, LOCK_UN); fclose($fp); }
    }

    /** Per-site status changes of the last $days days, newest first: [{t, d, from, to}] */
    public static function changes(int $days = 92): array
    {
        $since = date('Y-m-d', strtotime("-$days days"));
        return array_values(array_reverse(array_filter(self::load()['changes'], fn($c) => $c['t'] >= $since)));
    }

    /**
     * Add older status changes, found in the scan logs, to every history (server, resellers, customers)
     * whose site list has that site. Already known changes are skipped, so it is safe to run again.
     * Returns the number of changes added to the server history.
     */
    public static function backfillFromLogs(): int
    {
        $map = ['clean' => 'clean', 'schoon' => 'clean', 'suspicious' => 'suspicious', 'verdacht' => 'suspicious', 'infected' => 'infected', 'besmet' => 'infected'];
        $ev  = [];
        foreach (glob(VtDataDir::path('cron_*.log')) ?: [] as $f) {
            foreach (@file($f) ?: [] as $l) {
                if (preg_match('/^\[([\d\- :]{19})\] (?:\[(?:manual|scan)\] )?(?:\[(?:ok|\?|!)\] )?(clean|schoon|suspicious|verdacht|infected|besmet)\s+([a-z0-9.\-]+\.[a-z]{2,})/i', $l, $m)) {
                    $ev[] = [$m[1], strtolower($m[3]), $map[strtolower($m[2])]];
                }
            }
        }
        usort($ev, fn($a, $b) => strcmp($a[0], $b[0]));
        $found = []; $state = []; $seen = [];
        foreach ($ev as [$t, $d, $st]) {
            if (isset($state[$d]) && $state[$d] !== $st) $found[] = ['t' => $t, 'd' => $d, 'from' => $state[$d], 'to' => $st];
            $state[$d] = $st; $seen[$d] = $t;
        }
        $keep  = self::$scope;
        $added = 0;
        foreach (array_merge(['admin'], self::scopes()) as $scope) {
            self::scope($scope);
            $h = self::load();
            if (!$h['last']) continue;
            $known = [];
            foreach ($h['changes'] as $c) $known[$c['d'] . '|' . $c['to'] . '|' . substr($c['t'], 0, 10)] = 1;
            $n = 0;
            foreach ($found as $c) {
                if (!isset($h['last'][$c['d']]) || isset($known[$c['d'] . '|' . $c['to'] . '|' . substr($c['t'], 0, 10)])) continue;
                $h['changes'][] = $c; $n++;
            }
            // Server only: infected or suspicious sites that left the list after the history started
            if ($scope === 'admin' && $h['days']) {
                $real  = array_keys(array_filter($h['days'], fn($v) => empty($v['demo'])));
                $start = $real ? min($real) : '9999';
                foreach ($state as $d => $st) {
                    if (isset($h['last'][$d]) || !in_array($st, ['infected', 'suspicious'], true) || $seen[$d] < $start) continue;
                    if (isset($known[$d . '|gone|' . substr($seen[$d], 0, 10)])) continue;
                    $h['changes'][] = ['t' => $seen[$d], 'd' => $d, 'from' => $st, 'to' => 'gone', 'approx' => true]; $n++;
                }
            }
            if ($n) self::save($h);
            if ($scope === 'admin') $added = $n;
        }
        self::scope($keep);
        return $added;
    }

    /** The last $days days, oldest first: [{date, infected, suspicious, clean, total, cleaned, new_infected, demo}] */
    public static function days(int $days = 92): array
    {
        $h   = self::load();
        $out = [];
        foreach (array_slice($h['days'], -$days, null, true) as $date => $v) $out[] = ['date' => $date] + $v;
        return $out;
    }

    /**
     * Fill the days before today with made-up but plausible data (marked demo),
     * ending at the given totals. Only days that have no real data are filled.
     * Shape: many infections, a big clean-up about two months ago, then a slow rise again.
     */
    public static function seedDemo(int $days, int $infected, int $suspicious, int $total): void
    {
        $h = self::load();
        mt_srand(20261117);
        // The shape is drawn for a server of about 275 sites and scaled to this one
        $f   = $total / 275;
        $rnd = fn(int $a, int $b) => mt_rand($a, $b) * $f;
        $cleanupStart = (int)round($days * 0.66); // days ago
        for ($ago = $days; $ago >= 1; $ago--) {
            $date = date('Y-m-d', strtotime("-$ago days"));
            if (isset($h['days'][$date]) && empty($h['days'][$date]['demo'])) continue;
            if ($ago > $cleanupStart) {                       // before: high, drifting up
                $inf = (64 + ($days - $ago) * 0.35) * $f + $rnd(-3, 3);
                $sus = 18 * $f + $rnd(-2, 3);
                $cln = $rnd(0, 2);
            } elseif ($ago > $cleanupStart - 6) {             // the clean-up week
                $k   = ($cleanupStart - $ago + 1) / 6;
                $inf = (75 - 64 * $k) * $f + $rnd(-2, 2);
                $sus = (20 - 15 * $k) * $f + $rnd(-1, 1);
                $cln = $rnd(9, 16);
            } else {                                          // after: slow rise to today
                $k   = 1 - ($ago - 1) / max(1, $cleanupStart - 7);
                $inf = 10 * $f + ($infected - 10 * $f) * $k + $rnd(-2, 2);
                $sus = 4 * $f + ($suspicious - 4 * $f) * $k + $rnd(-1, 2);
                $cln = $rnd(0, 3);
            }
            $inf = (int)round($inf); $sus = (int)round($sus); $cln = (int)round($cln);
            $inf = min($total, max(0, $inf)); $sus = min($total - $inf, max(0, $sus)); // never more than there are sites
            $h['days'][$date] = ['infected' => $inf, 'suspicious' => $sus, 'clean' => max(0, $total - $inf - $sus),
                                 'total' => $total, 'cleaned' => $cln, 'new_infected' => (int)round($rnd(0, 3)), 'demo' => true];
        }
        self::save($h);
    }
}
