<?php
/**
 * Daily totals per scope: 'admin' = the whole server, 'r:<login>' = a reseller's customers and
 * own sites, 'c:<login>' = one customer's sites. Infected, suspicious and clean sites, plus how many
 * sites were cleaned (infected/suspicious -> clean) and newly infected that day.
 * Used for the trend graph. Stored in vt_history.json (server), vt_history_r_<login>.json (reseller)
 * or vt_history_c_<login>.json (customer):
 *   days: { "2026-11-17": {infected, suspicious, clean, total, cleaned, new_infected, demo?} }
 *   last: { domain: status }  (statuses at the previous snapshot, to count changes)
 */
require_once __DIR__ . '/VtDataDir.php';
require_once __DIR__ . '/VtEvents.php';

class VtHistory
{
    const KEEP_DAYS = 400;

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
        return is_array($d) ? $d + ['days' => [], 'last' => []] : ['days' => [], 'last' => []];
    }

    private static function save(array $h): void
    {
        ksort($h['days']);
        $h['days'] = array_slice($h['days'], -self::KEEP_DAYS, null, true);
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
            if (in_array($st, ['infected', 'suspicious', 'clean'], true)) $h['last'][$d] = $st;
        }
        $day = $h['days'][$today] ?? [];
        if (!empty($day['demo'])) $day = []; // real data replaces demo data
        $h['days'][$today] = $c + [
            'total'        => count($statusByDomain),
            'cleaned'      => (int)($day['cleaned'] ?? 0) + $cleaned,
            'new_infected' => (int)($day['new_infected'] ?? 0) + $newInf,
        ];
        // Forget sites that are gone; on the server history, note infected or suspicious ones in the feed
        if (self::$scope === 'admin') {
            foreach (array_diff_key($h['last'], $statusByDomain) as $d => $prev) {
                if (in_array($prev, ['infected', 'suspicious'], true)) VtEvents::gone((string)$d, $prev);
            }
        }
        $h['last'] = array_intersect_key($h['last'], $statusByDomain);
        self::save($h);
        if ($fp) { flock($fp, LOCK_UN); fclose($fp); }
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
