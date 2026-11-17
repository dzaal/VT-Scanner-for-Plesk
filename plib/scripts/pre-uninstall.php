<?php
/**
 * pre-uninstall.php — runs automatically when the extension is removed.
 * Removes the scheduled background scan. Scan data in
 * /usr/local/psa/var/modules/virustotal-extension/ is removed by Plesk.
 */
pm_Context::init('virustotal-extension');
pm_Scheduler::getInstance()->removeAllTasks();
exit(0);
