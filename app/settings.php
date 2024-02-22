<?php

$settings = require __DIR__ . '/defaults.php';

if(file_exists(__DIR__ . '/../config/local.php')) {
    require_once(__DIR__ . '/../config/local.php');
} elseif (file_exists(__DIR__ . '/../config/dev.php')) {
    require_once(__DIR__ . '/../config/dev.php');
} elseif (file_exists(__DIR__ . '/../config/test.php')) {
    require_once(__DIR__ . '/../config/test.php');
} elseif (file_exists(__DIR__ . '/../config/prod.php')) {
    require_once(__DIR__ . '/../config/prod.php');
}

//TODO: Move this to the container?
require_once __dir__  . "/version.php";

$settings['application']['version'] = VERSION_MAJOR . '.' . VERSION_MINOR . '.' . VERSION_PATCH . VERSION_TAG;

return $settings;
