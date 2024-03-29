<?php

$settings = [];

$settings['application'] = [
    'name' => 'IMS',
    'timezone' => 'UTC',
    'date_format' => 'c',
    'interval_format' => '%a minutes',
    'display_error_details' => false,
    'environment' => $_SERVER['APP_ENV'] ?? 'prod'
];

$settings['root'] = dirname(__DIR__);
$settings['temp'] = $settings['root'] . '/tmp';
$settings['public'] = $settings['root'] . '/public';
$settings['upload_dir'] = $settings['public'] . '/uploads';

$settings['debug'] = false;

$settings['error'] = [
    'display_error_details' => false,
    'log_errors' => true,
    'log_error_details' => false,
];

$settings['logger'] = [
    'name' => 'ims',
    'path' => $settings['root'] . '/logs',
    'filename' => 'app.log',
    'level' => 'info',
    'file_permission' => 0775,
];

$settings['twig'] = [
    'paths' => [
        __DIR__ . '/../templates',
    ],
    'options' => [
        'debug' => $settings['debug'],
        'cache_enabled' => !$settings['debug'],
        'cache_path' => $settings['temp'] . '/twig',
    ],
];

$settings['session'] = [
    'name' => 'ims',
    'cache_expire' => 0,
    'cookie_lifetime' => 2592000,
    'gc_maxlifetime' => 604800
];

$settings['database'] = [
    'database' => 'ims',
    'username' => 'root',
    'password' => '123',
    'host' => '127.0.0.1',
    'port' => 3306,
    'log_queries' => false
];

$settings['mail'] = [
    'dsn' => 'smtp://localhost:1025',
    'fromAddress' => 'system@this.website'
];

return $settings;
