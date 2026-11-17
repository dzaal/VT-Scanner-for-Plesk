<?php
/**
 * Fill the trend graph with demo data for the last 92 days (marked as demo), ending at
 * today's real totals. Days that already have real data are left alone, and real data
 * replaces demo data as it comes in. Never run automatically.
 *
 *   sudo -u psaadm /usr/local/psa/admin/bin/php .../scripts/seed_demo_history.php
 */
$plibDir = dirname(__DIR__) . '/library';
require_once $plibDir . '/VtDataDir.php';
require_once $plibDir . '/VtSettings.php';
require_once $plibDir . '/VtCache.php';
require_once $plibDir . '/PleskDomains.php';
require_once $plibDir . '/VtHistory.php';

$domains = PleskDomains::allDomains((bool)(new VtSettings('admin'))->get('wordpress_only', false));
$all     = (new VtCache())->all();
$c = ['infected' => 0, 'suspicious' => 0];
$status = [];
foreach ($domains as $d) {
    $st = (string)($all[$d]['status'] ?? 'unknown');
    $status[$d] = $st;
    if (isset($c[$st])) $c[$st]++;
}
VtHistory::seedDemo(92, $c['infected'], $c['suspicious'], count($domains));
VtHistory::record($status); // today: real
printf("Demo history filled: 92 days, ending at %d infected, %d suspicious of %d sites.\n", $c['infected'], $c['suspicious'], count($domains));
