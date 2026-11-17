<?php
/**
 * Fill the trend graph with demo data for the last 92 days (marked as demo, shaded in the
 * graph), ending at today's real totals. Days that already have real data are left alone,
 * and real data replaces demo data day by day. Never run automatically.
 *
 *   php seed_demo_history.php                 the server history (administrator)
 *   php seed_demo_history.php --resellers     also every reseller's history
 *   php seed_demo_history.php --customers     also every customer's history
 *   php seed_demo_history.php --remove        remove all demo days again (server, resellers, customers)
 *
 * Run as psaadm with Plesk's PHP:
 *   sudo -u psaadm /usr/local/psa/admin/bin/php .../scripts/seed_demo_history.php [options]
 */
$plibDir = dirname(__DIR__) . '/library';
require_once $plibDir . '/VtDataDir.php';
require_once $plibDir . '/VtSettings.php';
require_once $plibDir . '/VtCache.php';
require_once $plibDir . '/PleskDomains.php';
require_once $plibDir . '/VtHistory.php';

$opts = array_slice($argv, 1);

if (in_array('--remove', $opts, true)) {
    $n = 0;
    foreach (array_merge(['admin'], VtHistory::scopes()) as $scope) {
        VtHistory::scope($scope);
        $n += VtHistory::removeDemo();
    }
    printf("Removed %d demo days.\n", $n);
    exit(0);
}

$domains = PleskDomains::allDomains((bool)(new VtSettings('admin'))->get('wordpress_only', false));
$all     = (new VtCache())->all();
$status  = [];
foreach ($domains as $d) $status[$d] = (string)($all[$d]['status'] ?? 'unknown');

// Which histories to fill: the server, and optionally every reseller and/or customer
$scopes = ['admin' => $status];
if (in_array('--resellers', $opts, true) || in_array('--customers', $opts, true)) {
    foreach (PleskDomains::owners($domains) as $d => $o) {
        if (in_array('--resellers', $opts, true) && !empty($o['rl'])) $scopes['r:' . $o['rl']][$d] = $status[$d];
        if (in_array('--customers', $opts, true) && ($o['ct'] ?? '') === 'client') $scopes['c:' . $o['cl']][$d] = $status[$d];
    }
}

foreach ($scopes as $scope => $st) {
    $c = array_count_values($st);
    VtHistory::scope($scope);
    VtHistory::seedDemo(92, $c['infected'] ?? 0, $c['suspicious'] ?? 0, count($st));
    VtHistory::record($st); // today: real
}
printf("Demo history filled for %d histories: 92 days each, ending at today's real totals.\n", count($scopes));
