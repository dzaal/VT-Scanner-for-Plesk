<?php
/**
 * post-install.php — runs automatically when the extension is installed or upgraded.
 *
 * Registers the nightly background scan with Plesk's own scheduler, so no manual
 * cron entry is needed. Plesk runs the task as the panel user (psaadm) with its
 * bundled PHP, the same way Plesk's built-in extensions run their periodic tasks.
 *
 * Idempotent: existing tasks of this extension are removed first, so an upgrade
 * never leaves duplicate schedules behind.
 */
pm_Context::init('stingyscan');

// From VT Site Scanner (1.x, extension id "virustotal-extension") to StingyScan (2.x, "stingyscan"):
// Plesk sees a new extension, so copy settings, API keys, results, history and logs once from the
// old data folder. Files that already exist here are never overwritten. Plesk deletes the old folder
// when the old extension is uninstalled, so this must run first (it does: installing StingyScan).
$oldData = '/usr/local/psa/var/modules/virustotal-extension/data';
$newData = '/usr/local/psa/var/modules/stingyscan/data';
$marker  = $newData . '/.migrated_from_vt_site_scanner';
if ($oldData !== $newData && is_dir($oldData) && !file_exists($marker)) {
    if (!is_dir($newData)) @mkdir($newData, 0750, true);
    $n = 0;
    foreach (scandir($oldData) ?: [] as $f) {
        if ($f === '.' || $f === '..' || !is_file("$oldData/$f")) continue;
        if (preg_match('/\.(lock|tmp)$/', $f) || $f === 'vt_update_check.json') continue; // runtime files
        if (!file_exists("$newData/$f") && @copy("$oldData/$f", "$newData/$f")) $n++;
    }
    @file_put_contents($marker, date('c') . " copied $n files from $oldData\n");
    // The panel user must own the data (this script may run as root)
    if (function_exists('posix_geteuid') && posix_geteuid() === 0) {
        @exec('chown -R psaadm:psaadm ' . escapeshellarg(dirname($newData)));
    }
    echo "StingyScan: copied $n data files from VT Site Scanner. Uninstall the old extension: plesk bin extension --uninstall virustotal-extension\n";
}

$scheduler = pm_Scheduler::getInstance();
$scheduler->removeAllTasks();

$task = new pm_Scheduler_Task();
// Every night at 03:00 server time
$task->setSchedule([
    'minute' => '0',
    'hour'   => '3',
    'dom'    => '*',
    'month'  => '*',
    'dow'    => '*',
]);
$task->setCmd('background_scan.php');
$scheduler->putTask($task);

// Every 5 minutes: process the manual rescan queue (domains queued by clicking Rescan)
$task2 = new pm_Scheduler_Task();
$task2->setSchedule([
    'minute' => '*/5',
    'hour'   => '*',
    'dom'    => '*',
    'month'  => '*',
    'dow'    => '*',
]);
$task2->setCmd('manual_scan.php');
$scheduler->putTask($task2);

echo "StingyScan: nightly background scan scheduled (03:00), manual queue processor scheduled (every 5 min).\n";
exit(0);
