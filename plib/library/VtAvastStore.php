<?php
require_once __DIR__ . '/VtDataDir.php';
require_once __DIR__ . '/VtAvast.php';
require_once __DIR__ . '/VtSettings.php';
require_once __DIR__ . '/VtSiteLog.php';
require_once __DIR__ . '/VtCheckRateLimiter.php';

/**
 * Latest Avast / AVG classification per site, in vt_avast.json { domain: result of VtAvast::check() }.
 * The check runs live: when the details of a site are opened, and after a rescan. A result younger
 * than LIVE_REUSE seconds is reused, so opening and closing the details does not ask again.
 */
class VtAvastStore
{
    const LIVE_REUSE = 300;

    /** Server-wide setting of the administrator: the products switched on, [146 Avast, 147 AVG] */
    public static function products(): array
    {
        $s = new VtSettings('admin');
        $list = $s->get('avast_products');
        if (!is_array($list)) $list = (int)$s->get('avast_product', 0) ? [(int)$s->get('avast_product', 0)] : []; // before 1.29.0
        return array_values(array_filter(array_map('intval', $list), fn($p) => isset(VtAvast::PRODUCTS[$p])));
    }

    /** [domain => [product => result]] for these sites */
    public static function results(array $domains): array
    {
        return array_intersect_key(self::load(), array_flip($domains));
    }

    /** Check a site now (or reuse a fresh result unless $force), save it and note changes in the site history */
    public static function live(string $domain, int $product, bool $force = false): array
    {
        $prev = self::load()[$domain][$product] ?? null;
        if (!$force && $prev && time() - (int)($prev['checked_at'] ?? 0) < self::LIVE_REUSE) {
            return $prev + ['reused' => true];
        }
        $wait = VtCheckRateLimiter::reserve($product === 147 ? 'avg' : 'avast');
        if ($wait) return ['state' => 'rate_limited', 'retry_after' => $wait];
        $r = VtAvast::check($domain, $product);
        self::locked(function () use ($domain, $product, $r) {
            $all = self::load(); $all[$domain][$product] = $r; self::save($all);
        });
        $name = VtAvast::PRODUCTS[$product];
        $log  = fn(string $m) => @file_put_contents(VtDataDir::path('cron_admin.log'), '[' . date('Y-m-d H:i:s') . "] [$name] $m" . PHP_EOL, FILE_APPEND);
        if ($r['state'] !== 'ok') { $log("check failed $domain: " . ($r['error'] ?? 'unknown') . " (HTTP {$r['http']})"); return $r; }
        $now = implode(', ', array_column($r['findings'], 'classification'));
        $log(($now === '' ? 'no classification' : "classification $now") . " $domain");
        // Seed the history from an older successful cache when upgrading from a version
        // that did not record clear observations; retain evidence of a true removal.
        if (($prev['state'] ?? '') === 'ok' && !empty($prev['checked_at'])) {
            VtSiteLog::avastResult($domain, $name, implode(', ', array_column($prev['findings'] ?? [], 'classification')),
                                 $prev['findings'][0]['detection'] ?? '', (int)$prev['checked_at']);
        }
        VtSiteLog::avastResult($domain, $name, $now, $r['findings'][0]['detection'] ?? '', (int)$r['checked_at']);
        return $r;
    }

    /** Import existing successful snapshots at their recorded check time, without inventing a delisting. */
    public static function recordCachedObservations(): void
    {
        $marker = VtDataDir::path('vt_avast_observations_v1');
        if (is_file($marker)) return;
        foreach (self::load() as $domain => $products) {
            foreach ($products as $product => $r) {
                if (!isset(VtAvast::PRODUCTS[$product]) || ($r['state'] ?? '') !== 'ok' || empty($r['checked_at'])) continue;
                VtSiteLog::avastResult($domain, VtAvast::PRODUCTS[$product], implode(', ', array_column($r['findings'] ?? [], 'classification')),
                                     $r['findings'][0]['detection'] ?? '', (int)$r['checked_at']);
            }
        }
        @touch($marker);
    }

    private static function locked(callable $fn): void
    {
        $fp = @fopen(VtDataDir::path('vt_avast.json.lock'), 'c');
        if ($fp) flock($fp, LOCK_EX);
        try { $fn(); } finally { if ($fp) { flock($fp, LOCK_UN); fclose($fp); } }
    }

    private static function load(): array
    {
        $d = json_decode((string)@file_get_contents(VtDataDir::path('vt_avast.json')), true);
        if (!is_array($d)) return [];
        foreach ($d as $dom => $v) { // before 1.29.0: one result per site, with its product inside
            if (is_array($v) && isset($v['product'], $v['state'])) $d[$dom] = [(int)$v['product'] => $v];
        }
        return $d;
    }

    private static function save(array $data): void
    {
        $f = VtDataDir::path('vt_avast.json');
        if (@file_put_contents($f . '.tmp', json_encode($data)) !== false) @rename($f . '.tmp', $f);
    }
}
