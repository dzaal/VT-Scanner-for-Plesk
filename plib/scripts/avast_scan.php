<?php
/**
 * avast_scan.php — Avast / AVG classification of every site (optional, Settings > Avast / AVG).
 *
 * Scheduled by post-install.php every 5 minutes. Does nothing when the check is switched off.
 * Each run: first the sites asked for by hand ("Check now"), then sites whose result is due,
 * for at most RUN_SECONDS. All requests go through VtAvast's shared limiter (one per second,
 * never two at once), and only one run is active at a time.
 *
 * Manual run (for testing):
 *   sudo -u psaadm /usr/local/psa/admin/bin/php \
 *     /usr/local/psa/admin/plib/modules/virustotal-extension/scripts/avast_scan.php
 */
define('VT_CRON', true);
const RUN_SECONDS = 200;

$plibDir = dirname(__DIR__) . '/library';
require_once $plibDir . '/VtDataDir.php';
require_once $plibDir . '/VtSettings.php';
require_once $plibDir . '/PleskDomains.php';
require_once $plibDir . '/VtAvastStore.php';
require_once $plibDir . '/VtSiteLog.php';

$product = VtAvastStore::product();
if (!$product) exit(0);

$run = fopen(VtDataDir::path('vt_avast_scan.lock'), 'c');
if (!$run || !flock($run, LOCK_EX | LOCK_NB)) exit(0); // the previous run is still busy

$name    = VtAvast::PRODUCTS[$product];
$domains = PleskDomains::allDomains((bool)(new VtSettings('admin'))->get('wordpress_only', false));
if (!$domains) exit(0);
VtAvastStore::prune($domains);

$all  = VtAvastStore::all();
$todo = array_values(array_intersect(VtAvastStore::queued(), $domains));
foreach ($domains as $d) if (!in_array($d, $todo, true) && VtAvastStore::due($all, $d, $product)) $todo[] = $d;
// Oldest results first
$queued = array_flip(VtAvastStore::queued());
usort($todo, fn($a, $b) => [!isset($queued[$a]), $all[$a]['checked_at'] ?? 0] <=> [!isset($queued[$b]), $all[$b]['checked_at'] ?? 0]);

$log = fn(string $m) => @file_put_contents(VtDataDir::path('cron_admin.log'), '[' . date('Y-m-d H:i:s') . "] [$name] $m" . PHP_EOL, FILE_APPEND);
$t0 = time(); $n = 0;
foreach ($todo as $d) {
    if (time() - $t0 >= RUN_SECONDS) break;
    $r    = VtAvast::check($d, $product);
    $prev = VtAvastStore::put($d, $r);
    $n++;
    if ($r['state'] !== 'ok') { $log("check failed $d: " . ($r['error'] ?? 'unknown') . " (HTTP {$r['http']})"); continue; }
    $now = implode(', ', array_column($r['findings'], 'classification'));
    $log(($now === '' ? 'no classification' : "classification $now") . " $d");
    // History: when the classification appears, changes or goes away
    if (($prev['state'] ?? '') === 'ok' || $now !== '') {
        $was = ($prev['state'] ?? '') === 'ok' && (int)($prev['product'] ?? 0) === $product ? implode(', ', array_column($prev['findings'], 'classification')) : '';
        if ($was !== $now) VtSiteLog::avast($d, $name, $was, $now, $r['findings'][0]['detection'] ?? '');
    }
    if (!empty($r['http']) && $r['http'] === 429) break;
}
if ($n && function_exists('posix_isatty') && posix_isatty(STDOUT)) echo "$name: checked $n of " . count($todo) . " due\n";
flock($run, LOCK_UN);
