<?php
require_once __DIR__ . '/VtDataDir.php';
require_once __DIR__ . '/VtAvast.php';
require_once __DIR__ . '/VtSettings.php';
require_once __DIR__ . '/VtSiteLog.php';

/**
 * Latest Avast / AVG classification per site, in vt_avast.json { domain: result of VtAvast::check() }.
 * The check runs live: when the details of a site are opened, and after a rescan. A result younger
 * than LIVE_REUSE seconds is reused, so opening and closing the details does not ask again.
 */
class VtAvastStore
{
    const LIVE_REUSE = 300;

    /** Server-wide setting of the administrator: 0 = off, 146 = Avast, 147 = AVG */
    public static function product(): int
    {
        $p = (int)(new VtSettings('admin'))->get('avast_product', 0);
        return isset(VtAvast::PRODUCTS[$p]) ? $p : 0;
    }

    public static function results(array $domains): array
    {
        return array_intersect_key(self::load(), array_flip($domains));
    }

    /** Check a site now (or reuse a fresh result unless $force), save it and note changes in the site history */
    public static function live(string $domain, int $product, bool $force = false): array
    {
        $prev = self::load()[$domain] ?? null;
        if (!$force && $prev && (int)($prev['product'] ?? 0) === $product && time() - (int)($prev['checked_at'] ?? 0) < self::LIVE_REUSE) {
            return $prev + ['reused' => true];
        }
        $r = VtAvast::check($domain, $product);
        self::locked(function () use ($domain, $r) {
            $all = self::load(); $all[$domain] = $r; self::save($all);
        });
        $name = VtAvast::PRODUCTS[$product];
        $log  = fn(string $m) => @file_put_contents(VtDataDir::path('cron_admin.log'), '[' . date('Y-m-d H:i:s') . "] [$name] $m" . PHP_EOL, FILE_APPEND);
        if ($r['state'] !== 'ok') { $log("check failed $domain: " . ($r['error'] ?? 'unknown') . " (HTTP {$r['http']})"); return $r; }
        $now = implode(', ', array_column($r['findings'], 'classification'));
        $log(($now === '' ? 'no classification' : "classification $now") . " $domain");
        $was = ($prev['state'] ?? '') === 'ok' && (int)($prev['product'] ?? 0) === $product ? implode(', ', array_column($prev['findings'], 'classification')) : null;
        // History: the first classification, and every change after that
        if (($was === null && $now !== '') || ($was !== null && $was !== $now)) {
            VtSiteLog::avast($domain, $name, (string)$was, $now, $r['findings'][0]['detection'] ?? '');
        }
        return $r;
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
        return is_array($d) ? $d : [];
    }

    private static function save(array $data): void
    {
        $f = VtDataDir::path('vt_avast.json');
        if (@file_put_contents($f . '.tmp', json_encode($data)) !== false) @rename($f . '.tmp', $f);
    }
}
