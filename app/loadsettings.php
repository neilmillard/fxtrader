<?php
function loadsettings()
{

    // Instantiate the app
    $path = __DIR__ . '/../app/settings.php';
    if (!file_exists($path)) {
        $settings = require __DIR__ . '/../app/settings_dist.php';
    } else {
        $settings = require $path;
    }
    return $settings;
}
$settings = loadsettings();