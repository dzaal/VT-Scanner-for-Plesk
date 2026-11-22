<?php
require_once __DIR__ . '/VtDataDir.php';
require_once __DIR__ . '/VtSettings.php';

/** Server-wide spacing and rolling-minute limits for each optional provider/scanner. */
class VtCheckRateLimiter
{
    public const SOURCES = ['avg', 'avast', 'google', 'imunify', 'maldet', 'clamav'];
    public const DEFAULT_RATE = 2;

    public static function rates(): array
    {
        $saved = (new VtSettings('admin'))->get('check_rates', []);
        $out = [];
        foreach (self::SOURCES as $source) $out[$source] = max(1, min(5, (int)($saved[$source] ?? self::DEFAULT_RATE)));
        return $out;
    }

    /** Reserve an actual request/start; return seconds to wait when no slot is available. */
    public static function reserve(string $source, ?float $now = null): int
    {
        if (!in_array($source, self::SOURCES, true)) throw new InvalidArgumentException('Unknown check source');
        $rate = self::rates()[$source];
        $file = VtDataDir::path('vt_check_rate_' . $source . '.json');
        $fp = fopen($file, 'c+');
        if (!$fp || !flock($fp, LOCK_EX)) throw new RuntimeException('Check rate limiter unavailable');
        try {
            $now = $now ?? microtime(true);
            $starts = json_decode((string)stream_get_contents($fp), true);
            $starts = array_values(array_filter(is_array($starts) ? $starts : [], fn($t) => is_numeric($t) && $t > $now - 60));
            sort($starts, SORT_NUMERIC);
            $next = $starts ? end($starts) + 60 / $rate : $now;
            if (count($starts) >= $rate) $next = max($next, $starts[count($starts) - $rate] + 60);
            if ($next > $now) return max(1, (int)ceil($next - $now));
            $starts[] = $now;
            rewind($fp); ftruncate($fp, 0);
            fwrite($fp, json_encode($starts)); fflush($fp);
            return 0;
        } finally {
            flock($fp, LOCK_UN); fclose($fp);
        }
    }
}
