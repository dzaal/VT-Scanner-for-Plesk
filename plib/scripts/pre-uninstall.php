<?php
/**
 * pre-uninstall.php — runs automatically when the extension is removed.
 * Removes the scheduled background scan. Scan data in
 * /usr/local/psa/var/modules/stingyscan/ is removed by Plesk.
 */
pm_Context::init('stingyscan');
pm_Scheduler::getInstance()->removeAllTasks();
exit(0);
