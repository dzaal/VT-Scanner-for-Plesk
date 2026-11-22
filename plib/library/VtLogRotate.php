<?php
require_once __DIR__ . '/VtDataDir.php';
require_once __DIR__ . '/VtSettings.php';

/**
 * VtLogRotate — keeps the scan logs (cron_<user>.log) small.
 *
 * A log larger than log_max_mb is renamed and gzipped to cron_<user>.log.1.gz (older copies move up
 * to .2.gz, .3.gz, …). At most log_keep old copies are kept, and none older than log_max_days days.
 * Server-wide settings of the administrator. Runs from the cron scripts (every 5 minutes and nightly);
 * it only looks at file sizes, so it is cheap when there is nothing to do.
 */
class VtLogRotate
{
    const DEFAULTS = ['log_max_mb' => 5, 'log_keep' => 5, 'log_max_days' => 90];
    const LIMITS   = ['log_max_mb' => [1, 100], 'log_keep' => [1, 30], 'log_max_days' => [1, 365]];

    /** The settings, from the administrator's settings, within LIMITS */
    public static function settings(): array
    {
        $s = new VtSettings('admin');
        $out = [];
        foreach (self::DEFAULTS as $k => $def) $out[$k] = self::clamp($k, $s->get($k, $def));
        return $out;
    }

    public static function clamp(string $key, $value): int
    {
        [$min, $max] = self::LIMITS[$key];
        return max($min, min($max, (int)$value));
    }

    /** Rotate where needed; returns how many logs were rotated */
    public static function run(): int
    {
        $lock = @fopen(VtDataDir::path('vt_logrotate.lock'), 'c');
        if (!$lock || !flock($lock, LOCK_EX | LOCK_NB)) return 0; // another run is busy
        try {
            $cfg = self::settings();
            $max = $cfg['log_max_mb'] * 1048576;
            $n = 0;
            foreach (glob(VtDataDir::path('cron_*.log')) ?: [] as $log) {
                clearstatcache(true, $log);
                if (@filesize($log) > $max && self::rotate($log, $cfg['log_keep'])) $n++;
                self::prune($log, $cfg['log_keep'], $cfg['log_max_days']);
            }
            return $n;
        } finally {
            flock($lock, LOCK_UN); fclose($lock);
        }
    }

    /** Current size of the logs and of the old copies, for the Settings page */
    public static function info(): array
    {
        $cur = glob(VtDataDir::path('cron_*.log')) ?: [];
        $old = glob(VtDataDir::path('cron_*.log.*.gz')) ?: [];
        $sum = fn(array $fs) => array_sum(array_map(fn($f) => (int)@filesize($f), $fs));
        return ['logs' => count($cur), 'logs_bytes' => $sum($cur), 'old' => count($old), 'old_bytes' => $sum($old)];
    }

    private static function rotate(string $log, int $keep): bool
    {
        // Move older copies up: .4.gz -> .5.gz, …, .1.gz -> .2.gz
        for ($i = $keep - 1; $i >= 1; $i--) {
            if (is_file("$log.$i.gz")) @rename("$log.$i.gz", "$log." . ($i + 1) . '.gz');
        }
        // Rename first (atomic), so new lines go to a fresh log while the old one is compressed
        $tmp = "$log.rotating";
        if (!@rename($log, $tmp)) return false;
        $in = @fopen($tmp, 'rb'); $out = @gzopen("$log.1.gz", 'wb6');
        if ($in && $out) {
            while (!feof($in)) gzwrite($out, (string)fread($in, 1048576));
        }
        if ($in) fclose($in);
        if ($out) gzclose($out);
        @unlink($tmp);
        @file_put_contents($log, '[' . date('Y-m-d H:i:s') . '] [log] Rotated; the previous log is ' . basename($log) . '.1.gz' . PHP_EOL, FILE_APPEND);
        return true;
    }

    private static function prune(string $log, int $keep, int $days): void
    {
        $cutoff = time() - $days * 86400;
        foreach (glob("$log.*.gz") ?: [] as $f) {
            if (!preg_match('/\.(\d+)\.gz$/', $f, $m)) continue;
            if ((int)$m[1] > $keep || @filemtime($f) < $cutoff) @unlink($f);
        }
    }
}
