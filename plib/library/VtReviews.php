<?php
require_once __DIR__ . '/VtDataDir.php';

/**
 * VtReviews — remembers when a review was requested from a vendor for a site
 * (a click on the vendor's review link in the Why? panel).
 *
 * Stored in vt_reviews.json as:
 *   { "example.com": { "BitDefender": {"ts": 1795297156, "by": "admin"}, ... }, ... }
 */
class VtReviews
{
    private const FILE = 'vt_reviews.json';

    /** All requests for these domains: [domain => [vendor => {ts, by}]] */
    public static function forDomains(array $domains): array
    {
        return array_intersect_key(self::load(), array_flip($domains));
    }

    /** Record (or with $remove, forget) a review request; returns the requests of that domain */
    public static function mark(string $domain, string $vendor, string $by, bool $remove = false): array
    {
        $fp = @fopen(VtDataDir::path(self::FILE . '.lock'), 'c');
        if ($fp) flock($fp, LOCK_EX);
        $all = self::load();
        if ($remove) {
            unset($all[$domain][$vendor]);
            if (empty($all[$domain])) unset($all[$domain]);
        } else {
            $all[$domain][$vendor] = ['ts' => time(), 'by' => $by];
        }
        $f = VtDataDir::path(self::FILE);
        if (@file_put_contents($f . '.tmp', json_encode($all)) !== false) @rename($f . '.tmp', $f);
        if ($fp) { flock($fp, LOCK_UN); fclose($fp); }
        return $all[$domain] ?? [];
    }

    private static function load(): array
    {
        $d = json_decode((string)@file_get_contents(VtDataDir::path(self::FILE)), true);
        return is_array($d) ? $d : [];
    }
}
