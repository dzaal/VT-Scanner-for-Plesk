<?php
require_once __DIR__ . '/VtDataDir.php';
require_once __DIR__ . '/VtEvents.php';

/**
 * VtSiteLog — history per site: when vendors started or stopped flagging it, and every
 * review request (which vendor, by website form or e-mail, who). Status changes come from VtEvents.
 *
 * Stored in vt_sitelog.json as { domain: [ entry, ... ] }, oldest first. Entry types:
 *   detected  {ts, score, vendors[], partial?}  first time the site was seen flagged
 *   flag      {ts, vendor, verdict}             a vendor started flagging the site
 *   unflag    {ts, vendor}                      a vendor no longer flags it
 *   review    {ts, vendor, via: form|email, to, by}
 *   unreview  {ts, vendor, by}                  a review mark was undone
 *   denied    {ts, vendor, reason, by}          the vendor turned the review request down
 *   av        {ts, product, from, to, detection} Avast / AVG classification appeared, changed or went away
 */
class VtSiteLog
{
    private const FILE = 'vt_sitelog.json';
    private const KEEP = 300; // entries per site

    /** Compare the previous and the new scan result of a site and log vendor changes */
    public static function recordScan(string $domain, ?array $old, array $new): void
    {
        if (!in_array($new['status'] ?? '', ['clean', 'suspicious', 'infected'], true)) return;
        $now = self::vendors($new['flagged_by'] ?? []);
        $had = is_array($old) && array_key_exists('flagged_by', $old) ? self::vendors($old['flagged_by'] ?? []) : null;
        $add = [];
        if ($had === null) {
            if ($now && !self::has($domain, 'detected')) {
                $add[] = ['type' => 'detected', 'score' => ($new['malicious'] ?? 0) . '/' . ($new['total'] ?? 0), 'vendors' => array_keys($now)];
            }
        } else {
            foreach (array_diff_key($now, $had) as $v => $verdict) $add[] = ['type' => 'flag', 'vendor' => $v, 'verdict' => $verdict];
            foreach (array_diff_key($had, $now) as $v => $verdict) $add[] = ['type' => 'unflag', 'vendor' => $v];
        }
        if ($add) self::add($domain, array_map(fn($e) => ['ts' => time()] + $e, $add));
    }

    /** A review was requested from a vendor (or the mark was undone) */
    public static function review(string $domain, string $vendor, string $via, string $to, string $by, bool $undo = false, ?string $deniedReason = null): void
    {
        if ($deniedReason !== null) $e = ['ts' => time(), 'type' => 'denied', 'vendor' => $vendor, 'reason' => $deniedReason, 'by' => $by];
        elseif ($undo) $e = ['ts' => time(), 'type' => 'unreview', 'vendor' => $vendor, 'by' => $by];
        else $e = ['ts' => time(), 'type' => 'review', 'vendor' => $vendor, 'via' => $via === 'email' ? 'email' : 'form', 'to' => $to, 'by' => $by];
        self::add($domain, [$e]);
    }

    /** The Avast / AVG classification of a site changed ('' = no classification) */
    public static function avast(string $domain, string $product, string $from, string $to, string $detection): void
    {
        self::add($domain, [['ts' => time(), 'type' => 'av', 'product' => $product, 'from' => $from, 'to' => $to, 'detection' => $detection]]);
    }

    /** Record successful direct checks, comparing against the last successful observation.
     * Failed requests never erase the baseline. A first clear check is an observation,
     * not evidence that a previously recorded classification was removed.
     */
    public static function avastResult(string $domain, string $product, string $classification, string $detection, int $ts): void
    {
        self::add($domain, [], function (array $list) use ($product, $classification, $detection, $ts): array {
            $previous = null;
            foreach ($list as $entry) {
                if (($entry['type'] ?? '') === 'av' && ($entry['product'] ?? '') === $product) $previous = $entry;
            }
            if ($previous && (($previous['ts'] ?? 0) > $ts || ($previous['to'] ?? '') === $classification)) return [];
            return [['ts' => $ts, 'type' => 'av', 'product' => $product,
                     'from' => $previous['to'] ?? null, 'to' => $classification, 'detection' => $detection,
                     'observed' => $previous === null]];
        });
    }

    /** Combined feed; direct product observations keep their own source and verdict. */
    public static function recentChanges(): array
    {
        $events = VtEvents::recent(5000);
        foreach (self::load() as $domain => $list) {
            foreach ($list as $entry) {
                if (($entry['type'] ?? '') === 'av' && ($entry['ts'] ?? 0) >= time() - 90 * 86400) {
                    $events[] = ['domain' => $domain] + $entry;
                }
            }
        }
        usort($events, fn($a, $b) => $b['ts'] <=> $a['ts']);
        return $events;
    }

    /** The whole history of a site, newest first, with the status changes merged in */
    public static function history(string $domain): array
    {
        $list = self::load()[$domain] ?? [];
        foreach (VtEvents::recent(5000) as $e) {
            if (($e['domain'] ?? '') === $domain) $list[] = ['ts' => $e['ts'], 'type' => 'status', 'from' => $e['from'], 'to' => $e['to'], 'approx' => !empty($e['approx'])];
        }
        usort($list, fn($a, $b) => $b['ts'] <=> $a['ts']);
        return $list;
    }

    /** Sites that have a history (to offer it also when a site is clean now) */
    public static function domains(): array
    {
        $d = array_keys(self::load());
        foreach (VtEvents::recent(5000) as $e) $d[] = $e['domain'] ?? '';
        return array_values(array_unique(array_filter($d)));
    }

    /**
     * Once: the first detection of every site, found in the scan logs (cron_*.log). Those lines
     * name only the first few vendors, so the entry is marked partial. Safe to run again.
     */
    public static function backfillFromLogs(): int
    {
        $first = [];
        foreach (glob(VtDataDir::path('cron_*.log')) ?: [] as $f) {
            foreach (@file($f) ?: [] as $l) {
                if (preg_match('/^\[([\d\- :]{19})\] (?:\[(?:manual|scan)\] )?(?:\[(?:\?|!)\] )?(?:suspicious|verdacht|infected|besmet)\s+([a-z0-9.\-]+\.[a-z]{2,}) \((\d+\/\d+)\)(?: — (.*))?$/iu', trim($l), $m)) {
                    $ts = strtotime($m[1]); $d = strtolower($m[2]);
                    if (isset($first[$d]) && $first[$d]['ts'] <= $ts) continue;
                    $vendors = array_values(array_filter(array_map(fn($s) => trim(explode(':', $s)[0]), explode(',', $m[4] ?? ''))));
                    $first[$d] = ['ts' => $ts, 'type' => 'detected', 'score' => $m[3], 'vendors' => $vendors, 'partial' => true];
                }
            }
        }
        $n = 0;
        foreach ($first as $d => $e) {
            if (self::has($d, 'detected')) continue;
            self::add($d, [$e]); $n++;
        }
        return $n;
    }

    /** ["BitDefender: phishing", ...] -> ["BitDefender" => "phishing", ...] */
    private static function vendors(array $flagged): array
    {
        $out = [];
        foreach ($flagged as $s) {
            $i = strpos((string)$s, ': ');
            if ($i === false) $out[(string)$s] = '';
            else $out[substr($s, 0, $i)] = substr($s, $i + 2);
        }
        return $out;
    }

    private static function has(string $domain, string $type): bool
    {
        foreach (self::load()[$domain] ?? [] as $e) if (($e['type'] ?? '') === $type) return true;
        return false;
    }

    private static function add(string $domain, array $entries, ?callable $build = null): void
    {
        $fp = @fopen(VtDataDir::path(self::FILE . '.lock'), 'c');
        if ($fp) flock($fp, LOCK_EX);
        $all = self::load();
        if ($build) $entries = $build($all[$domain] ?? []);
        if (!$entries) { if ($fp) { flock($fp, LOCK_UN); fclose($fp); } return; }
        $list = array_merge($all[$domain] ?? [], $entries);
        usort($list, fn($a, $b) => $a['ts'] <=> $b['ts']);
        $all[$domain] = array_slice($list, -self::KEEP);
        $f = VtDataDir::path(self::FILE);
        if (@file_put_contents($f . '.tmp', json_encode($all)) !== false) @rename($f . '.tmp', $f);
        if ($fp) { flock($fp, LOCK_UN); fclose($fp); }
    }

    private static function load(): array
    {
        $d = json_decode((string)@file_get_contents(VtDataDir::path(self::FILE)), true);
        return is_array($d) ? $d : [];
    }
}
