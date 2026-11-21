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

// Every 5 minutes: Avast / AVG classification (does nothing unless switched on in Settings)
$task3 = new pm_Scheduler_Task();
$task3->setSchedule(['minute' => '*/5', 'hour' => '*', 'dom' => '*', 'month' => '*', 'dow' => '*']);
$task3->setCmd('avast_scan.php');
$scheduler->putTask($task3);

echo "VT Site Scanner: nightly background scan scheduled (03:00), manual queue processor and Avast/AVG check scheduled (every 5 min).\n";
exit(0);
