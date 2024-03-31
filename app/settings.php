<?php

$settings = require __DIR__ . '/defaults.php';

// if(file_exists(__DIR__ . '/../config/local.php')) {
//     $settings = require_once(__DIR__ . '/../config/local.php');
// } elseif (file_exists(__DIR__ . '/../config/dev.php')) {
//     $settings = require_once(__DIR__ . '/../config/dev.php');
// } elseif (file_exists(__DIR__ . '/../config/test.php')) {
//     $settings = require_once(__DIR__ . '/../config/test.php');
// } elseif (file_exists(__DIR__ . '/../config/prod.php')) {
//     $settings = require_once(__DIR__ . '/../config/prod.php');
// }

// $config->merge($settings);

$configFiles = [
    __DIR__ . sprintf('/../config/%s.php', $_SERVER['APP_ENV'] ?? 'prod'),
    __DIR__ . sprintf('/../env.php')
];

foreach ($configFiles as $configFile) {
    if (!file_exists($configFile)) {
        continue;
    }

    $local = require $configFile;
    if (is_callable($local)) {
        $settings = $local($settings);
    }
}

//TODO: Move this to the container?
require_once __dir__  . "/version.php";

$settings['application']['version'] = VERSION_MAJOR . '.' . VERSION_MINOR . '.' . VERSION_PATCH . VERSION_TAG;

$settings['secret'] = $_ENV['APP_SECRET'] ?? throw new Exception("Application secret token is missing from .env");

$settings['root'] = dirname(__DIR__);
$settings['temp'] = $settings['root'] . '/tmp';
$settings['public'] = $settings['root'] . '/public';
$settings['upload_dir'] = $settings['public'] . '/uploads';

return $settings;
