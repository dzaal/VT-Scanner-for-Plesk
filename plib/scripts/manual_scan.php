<?php
/**
 * manual_scan.php — processes the server-side manual rescan queue.
 *
 * Scheduled by post-install.php to run every 5 minutes via Plesk's scheduler.
 * Picks up domains the browser submitted via ?action=enqueue-rescan but could not
 * finish before the user navigated away (or the browser closed).
 *
 * Manual run (for testing):
 *   sudo -u psaadm /usr/local/psa/admin/bin/php \
 *     /usr/local/psa/admin/plib/modules/stingyscan/scripts/manual_scan.php
 */

define('VT_CRON', true);

$plibDir = dirname(__DIR__) . '/library';
require_once $plibDir . '/VtDataDir.php';
require_once $plibDir . '/VtSettings.php';
require_once $plibDir . '/VtCache.php';
require_once $plibDir . '/VtRateLimiter.php';
require_once $plibDir . '/VtApi.php';
require_once $plibDir . '/VtManualQueue.php';
require_once $plibDir . '/VtNotifier.php';
require_once $plibDir . '/VtLogRotate.php';

// Keep the scan logs small (log rotation settings in Settings)
try { VtLogRotate::run(); } catch (\Throwable $e) {}

// One run at a time: a run can take longer than the 5 minutes between runs
$runLock = @fopen(VtDataDir::path('vt_manual_scan.lock'), 'c');
if (!$runLock || !flock($runLock, LOCK_EX | LOCK_NB)) exit(0);

$settings = new VtSettings();
$apiKey   = $settings->get('api_key');

if (!$apiKey) {
    exit(0); // No key: nothing to do, no noise
}

// Only rescans that no open page is still working on (see VtManualQueue::ORPHAN_AFTER)
$pending = VtManualQueue::takeable();
if (empty($pending)) {
    exit(0);
}

$api    = new VtApi($apiKey);
$cache  = new VtCache($settings);
$budget = $cache->budgetInfo();

// Each domain requires 2 API calls (reanalyze + fetch). Stay within manual budget.
$manualLeft = max(0, $budget['manual_max'] - $budget['manual_used']);
// Cap at 6 domains per 5-minute window (= 12 calls, well within 4 req/min if spaced)
$maxDomains = min(count($pending), (int)floor($manualLeft / 2), 6);

if ($maxDomains <= 0) {
    exit(0);
}

// VT free tier: 4 req/min → 15s between calls
$delay = (int)ceil(60 / VtSettings::VT_FREE_PER_MIN);

$logFile = VtDataDir::path('cron_admin.log');
$processed = [];

foreach (array_slice($pending, 0, $maxDomains) as $i => $domain) {

    // 1. Request a re-analysis
    if ($i > 0) sleep($delay);
    // A page may have picked it up again, or finished it, while this run was busy
    if (!in_array($domain, VtManualQueue::takeable(), true)) continue;

    $sub = $api->reanalyze($domain);

    if ($sub['status'] === 'quota_exceeded') {
        log_msg("[manual] Daily VT quota exhausted. Stopping.");
        break;
    }
    if ($sub['status'] === 'ratelimit') {
        $wait = $sub['wait'] ?? 60;
        log_msg("[manual] Rate limit on reanalyze for $domain — waiting {$wait}s…");
        sleep($wait);
        $sub = $api->reanalyze($domain);
        if ($sub['status'] !== 'queued') {
            log_msg("[manual] Still failed after wait: $domain ({$sub['status']})");
            continue;
        }
    }
    if ($sub['status'] !== 'queued') {
        // Not a temporary problem: take it off the queue instead of retrying every 5 minutes
        log_msg("[manual] Reanalyze failed: $domain ({$sub['status']}), removed from the queue");
        VtManualQueue::complete([$domain]);
        continue;
    }

    $cache->charge(true); // counts as a manual call

    // 2. Wait for VirusTotal to finish the analysis (~30s)
    sleep(30);

    // 3. Fetch the fresh report
    sleep($delay);
    $fresh = $api->getReport($domain);

    if ($fresh['status'] === 'quota_exceeded') {
        $cache->set($domain, $fresh, true);
        log_msg("[manual] Quota exhausted while fetching $domain. Stopping.");
        break;
    }
    if ($fresh['status'] === 'ratelimit') {
        $wait = $fresh['wait'] ?? 60;
        log_msg("[manual] Rate limit on fetch for $domain — waiting {$wait}s…");
        sleep($wait);
        $fresh = $api->getReport($domain);
    }

    $cache->set($domain, $fresh, true);

    $status = $fresh['status'] ?? 'error';
    $score  = isset($fresh['malicious'], $fresh['total']) ? " ({$fresh['malicious']}/{$fresh['total']})" : '';
    if ($status === 'infected') {
        log_msg("[manual] [!] INFECTED $domain$score");
    } elseif ($status === 'suspicious') {
        log_msg("[manual] [?] suspicious $domain$score");
    } elseif ($status === 'clean') {
        log_msg("[manual] [ok] clean $domain$score");
    } else {
        log_msg("[manual] $status $domain");
    }

    $processed[] = $domain;
}

if ($processed) {
    VtManualQueue::complete($processed);
    log_msg("[manual] Completed " . count($processed) . " domain(s): " . implode(', ', $processed));

    // Flush notification summaries if any status changes occurred
    try {
        $mailed = VtNotifier::flush(false);
        if ($mailed) log_msg('[manual][mail] summary sent to ' . implode(', ', $mailed));
    } catch (\Throwable $e) {
        log_msg('[manual][mail] could not send: ' . $e->getMessage());
    }
}

function log_msg(string $msg): void
{
    $line = '[' . date('Y-m-d H:i:s') . '] ' . $msg . PHP_EOL;
    if (function_exists('posix_isatty') && @posix_isatty(STDOUT)) echo $line;
    $logFile = VtDataDir::path('cron_admin.log');
    file_put_contents($logFile, $line, FILE_APPEND);
}
