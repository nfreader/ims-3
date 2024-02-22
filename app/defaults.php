<?php

use App\Domain\App\Enum\Environment;

$environment = Environment::PROD;

$settings = [];

$settings['application'] = [
    'name' => 'IMS',
    'timezone' => 'UTC',
    'date_format' => 'F j, Y \a\t H:i:s (e)',
    'interval_format' => '%a minutes'
];

$settings['root'] = dirname(__DIR__);
$settings['temp'] = $settings['root'] . '/tmp';
$settings['public'] = $settings['root'] . '/public';
$settings['upload_dir'] = $settings['public'] . '/uploads';

$settings['debug'] = $environment->enableDebug($environment);

$settings['error'] = [
    'display_error_details' => $environment->enableDebug($environment),
    'log_errors' => !$environment->enableDebug($environment),
    'log_error_details' => !$environment->enableDebug($environment),
];

$settings['logger'] = [
    'name' => 'ims',
    'path' => $settings['root'] . '/logs',
    'filename' => 'app.log',
    'level' => \Monolog\Logger::INFO,
    'file_permission' => 0775,
  ];

$settings['environment'] = [
    'name' => $environment,
];

$settings['twig'] = [
    'paths' => [
        __DIR__ . '/../templates',
    ],
    'options' => [
        'debug' => $environment->enableDebug($environment),
        'cache_enabled' => true,
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
  'flags' => [
      PDO::ATTR_PERSISTENT               => true,
      PDO::ATTR_ERRMODE                  => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE       => PDO::FETCH_OBJ,
      PDO::ATTR_STRINGIFY_FETCHES        => false,
      PDO::ATTR_EMULATE_PREPARES         => false
  ]
];

return $settings;
