<?php

use DI\ContainerBuilder;
use Slim\App;

require_once  __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__."/../");
$dotenv->load();
$dotenv->required(['APP_SECRET'])->notEmpty();

$containerBuilder = new ContainerBuilder();
$containerBuilder->useAttributes(true);
$containerBuilder->addDefinitions(__DIR__ . '/container.php');
$container = $containerBuilder->build();
// Create App instance
return $container->get(App::class);
