<?php

use DI\ContainerBuilder;
use Slim\App;

require_once  __DIR__ . '/../vendor/autoload.php';

$containerBuilder = new ContainerBuilder();
$containerBuilder->useAttributes(true);
$containerBuilder->addDefinitions(__DIR__ . '/container.php');
$container = $containerBuilder->build();
// Create App instance
return $container->get(App::class);
