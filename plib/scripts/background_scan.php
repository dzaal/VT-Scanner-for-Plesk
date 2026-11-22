<?php
/**
 * background_scan.php — nightly VirusTotal background scan.
 *
 * Scheduled automatically by post-install.php through Plesk's scheduler
 * (runs as psaadm with Plesk's PHP, every night at 03:00). No manual cron needed.
 *
 * Manual run (e.g. to test), as the panel user and with Plesk's PHP 8:
 *   sudo -u psaadm /usr/local/psa/admin/bin/php  *     /usr/local/psa/admin/plib/modules/stingyscan/scripts/background_scan.php
 *
 * Do NOT run it as root or with the OS's /usr/bin/php: files created as root
 * become unwritable for the panel, and older PHP versions can't parse this code.
 */

define('VT_CRON', true);

$plibDir = dirname(__DIR__) . '/library';
require_once $plibDir . '/VtDataDir.php';
require_once $plibDir . '/VtSettings.php';
require_once $plibDir . '/VtCache.php';
require_once $plibDir . '/VtRateLimiter.php';
require_once $plibDir . '/VtApi.php';
require_once $plibDir . '/PleskDomains.php';
require_once $plibDir . '/VtNotifier.php';
require_once $plibDir . '/VtHistory.php';
require_once $plibDir . '/VtLogRotate.php';
require_once $plibDir . '/VtSafeBrowsing.php';

// Keep the scan logs small (log rotation settings in Settings)
try { VtLogRotate::run(); } catch (\Throwable $e) {}

$settings = new VtSettings();
$apiKey   = $settings->get('api_key');

if (!$apiKey) {
    fwrite(STDERR, "[VT cron] No API key configured.\n");
    exit(1);
}

$api    = new VtApi($apiKey);
$cache  = new VtCache();
$budget = $cache->budgetInfo();

$vtRemaining = $api->remainingToday();
log_msg("Start — VT quota remaining: {$vtRemaining}/500 | budget: auto {$budget['auto_used']}/{$budget['auto_max']}, manual {$budget['manual_used']}/{$budget['manual_max']}");

$domains = PleskDomains::allDomains((bool)(new VtSettings('admin'))->get('wordpress_only', false));
if (empty($domains)) {
    log_msg("No domains found. Stopping.");
    exit(0);
}

log_msg(count($domains) . " domains loaded, queue_pos=" . $budget['queue_pos']);

// Google Safe Browsing (optional): every site, up to 250 per request, independent of the VirusTotal budget
if (VtSafeBrowsing::enabled()) {
    try {
        $listed = VtSafeBrowsing::checkAll($domains);
        log_msg('[Google Safe Browsing] ' . count($domains) . ' sites checked, ' . $listed . ' listed');
    } catch (\Throwable $e) {
        log_msg('[Google Safe Browsing] check failed: ' . $e->getMessage());
    }
}

$toScan = $cache->nextToScan($domains, $budget['auto_max']);

if (empty($toScan)) {
    log_msg("Nothing to scan (budget exhausted or all results fresh). Stopping.");
    exit(0);
}

log_msg(count($toScan) . " domains queued.");

// VT free tier: max 4 req/min → 15s tussen calls
$delay = (int)ceil(60 / VtSettings::VT_FREE_PER_MIN);

$counts = ['infected' => 0, 'suspicious' => 0, 'clean' => 0, 'error' => 0, 'other' => 0];

foreach ($toScan as $i => $domain) {
    if ($i > 0) sleep($delay);

    $result = $api->getReport($domain);

    if ($result['status'] === 'quota_exceeded') {
        $ra = $result['retry_after'] ?? null;
        $resets = $ra ? ' Resets in ' . gmdate('H\hi', $ra) . ' UTC.' : '';
        log_msg("Daily VT quota exhausted.$resets Stopping.");
        break;
    }
    if ($result['status'] === 'ratelimit') {
        $wait = $result['wait'] ?? 60;
        log_msg("Per-minute rate limit hit at $domain. Waiting {$wait}s...");
        sleep($wait);
        // Retry once after waiting
        $result = $api->getReport($domain);
        if ($result['status'] === 'ratelimit' || $result['status'] === 'quota_exceeded') {
            log_msg("Still rate-limited after wait. Stopping.");
            break;
        }
    }

    $cache->set($domain, $result, false);
    $status = $result['status'] ?? 'error';

    switch ($status) {
        case 'infected':
            $counts['infected']++;
            $vendors = !empty($result['flagged_by']) ? ' — ' . implode(', ', array_slice($result['flagged_by'], 0, 3)) : '';
            $score   = isset($result['malicious'], $result['total']) ? " ({$result['malicious']}/{$result['total']})" : '';
            log_msg("[!] INFECTED $domain$score$vendors");
            break;
        case 'suspicious':
            $counts['suspicious']++;
            $score = isset($result['malicious'], $result['total']) ? " ({$result['malicious']}/{$result['total']})" : '';
            log_msg("[?] suspicious $domain$score");
            break;
        case 'clean':
            $counts['clean']++;
            $score = isset($result['total']) ? " (0/{$result['total']})" : '';
            log_msg("[ok] clean $domain$score");
            break;
        case 'error':
            $counts['error']++;
            $extra = isset($result['http_code']) ? " HTTP {$result['http_code']}" : '';
            log_msg("[ERR] error $domain$extra");
            break;
        default:
            $counts['other']++;
            log_msg("[~] $status $domain");
    }
}

// Send the collected status changes as one summary e-mail per user
try {
    $mailed = VtNotifier::flush(true);
    if ($mailed) log_msg('[mail] summary sent to ' . implode(', ', $mailed));
} catch (\Throwable $e) {
    log_msg('[mail] could not send summary: ' . $e->getMessage());
}

$budget = $cache->budgetInfo();
log_msg(sprintf(
    "Done — infected: %d | suspicious: %d | clean: %d | error: %d | auto: %d/%d",
    $counts['infected'], $counts['suspicious'], $counts['clean'],
    $counts['error'], $budget['auto_used'], $budget['auto_max']
));

// Today's totals for the trend graph: the whole server, each reseller's sites and each customer's sites
$allNow = $cache->all();
$status = array_map(fn($d) => (string)($allNow[$d]['status'] ?? 'unknown'), array_combine($domains, $domains));
VtHistory::scope('admin');
VtHistory::record($status);
$perScope = [];
foreach (PleskDomains::owners($domains) as $d => $o) {
    if (!empty($o['rl'])) $perScope['r:' . $o['rl']][$d] = $status[$d];
    if (($o['ct'] ?? '') === 'client') $perScope['c:' . $o['cl']][$d] = $status[$d];
}
foreach ($perScope as $scope => $st) {
    VtHistory::scope($scope);
    VtHistory::record($st);
}
VtHistory::scope('admin');

function log_msg(string $msg): void
{
    $line = '[' . date('Y-m-d H:i:s') . '] ' . $msg . PHP_EOL;
    // Echo only when run by hand in a terminal; the scheduled run stays silent
    // (cron output would otherwise be mailed to the panel user).
    if (function_exists('posix_isatty') && @posix_isatty(STDOUT)) echo $line;
    // Admin log — cron output is only visible to admin, not resellers/clients
    $logFile = VtDataDir::path('cron_admin.log');
    file_put_contents($logFile, $line, FILE_APPEND);
}
