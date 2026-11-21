<?php
require_once __DIR__ . '/VtDataDir.php';
require_once __DIR__ . '/VtAvast.php';
require_once __DIR__ . '/VtSettings.php';

/**
 * Results and queue of the Avast / AVG classification check.
 *   vt_avast.json        { domain: result of VtAvast::check() }
 *   vt_avast_queue.json  [ domain, ... ]  sites asked for by hand ("Check now"), handled first
 * The check itself runs only in scripts/avast_scan.php (every 5 minutes), never in a page request.
 */
class VtAvastStore
{
    const TTL_OK       = 7 * 86400;  // no classification: check again after a week
    const TTL_DETECTED = 86400;      // a classification: every day, to see it go away after a review
    const TTL_FAILED   = 3600;       // failed: try again after an hour

    /** Server-wide setting of the administrator: 0 = off, 146 = Avast, 147 = AVG */
    public static function product(): int
    {
        $p = (int)(new VtSettings('admin'))->get('avast_product', 0);
        return isset(VtAvast::PRODUCTS[$p]) ? $p : 0;
    }

    public static function results(array $domains): array
    {
        return array_intersect_key(self::load('vt_avast.json'), array_flip($domains));
    }

    public static function queued(): array { return self::load('vt_avast_queue.json'); }

    public static function enqueue(string $domain): void
    {
        self::locked(function () use ($domain) {
            $q = self::load('vt_avast_queue.json');
            if (!in_array($domain, $q, true)) { $q[] = $domain; self::save('vt_avast_queue.json', $q); }
        });
    }

    /** Is this site due for a check with this product? */
    public static function due(array $all, string $domain, int $product): bool
    {
        $r = $all[$domain] ?? null;
        if (!$r || (int)($r['product'] ?? 0) !== $product) return true;
        $age = time() - (int)($r['checked_at'] ?? 0);
        if (($r['state'] ?? '') !== 'ok') return $age >= self::TTL_FAILED;
        return $age >= (empty($r['findings']) ? self::TTL_OK : self::TTL_DETECTED);
    }

    /** Save a result; returns the previous one */
    public static function put(string $domain, array $result): ?array
    {
        $prev = null;
        self::locked(function () use ($domain, $result, &$prev) {
            $all = self::load('vt_avast.json');
            $prev = $all[$domain] ?? null;
            $all[$domain] = $result;
            self::save('vt_avast.json', $all);
            $q = array_values(array_diff(self::load('vt_avast_queue.json'), [$domain]));
            self::save('vt_avast_queue.json', $q);
        });
        return $prev;
    }

    public static function all(): array { return self::load('vt_avast.json'); }

    /** Forget results of sites that are no longer in the list */
    public static function prune(array $domains): void
    {
        self::locked(function () use ($domains) {
            $all = self::load('vt_avast.json');
            $keep = array_intersect_key($all, array_flip($domains));
            if (count($keep) !== count($all)) self::save('vt_avast.json', $keep);
        });
    }

    private static function locked(callable $fn): void
    {
        $fp = @fopen(VtDataDir::path('vt_avast.json.lock'), 'c');
        if ($fp) flock($fp, LOCK_EX);
        try { $fn(); } finally { if ($fp) { flock($fp, LOCK_UN); fclose($fp); } }
    }

    private static function load(string $file): array
    {
        $d = json_decode((string)@file_get_contents(VtDataDir::path($file)), true);
        return is_array($d) ? $d : [];
    }

    private static function save(string $file, array $data): void
    {
        $f = VtDataDir::path($file);
        if (@file_put_contents($f . '.tmp', json_encode($data)) !== false) @rename($f . '.tmp', $f);
    }
}
