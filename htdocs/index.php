<?php
/**
 * Entry point voor VirusTotal Dashboard Plesk extension.
 *
 * Plesk's pm_Application regelt de MVC-routing EN toont de Plesk-layout
 * (inclusief linker navigatiebalk) in plaats van standalone fullscreen.
 */
pm_Context::init('stingyscan');

$application = new pm_Application();
$application->run();
