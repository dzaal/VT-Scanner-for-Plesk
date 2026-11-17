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
pm_Context::init('virustotal-extension');

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

echo "VT Site Scanner: nightly background scan scheduled (03:00).\n";
exit(0);
