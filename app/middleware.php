<?php

use App\Middleware\ExceptionHandlerMiddleware;
use App\Middleware\JsonBodyParserMiddleware;
use Middlewares\TrailingSlash;
use Slim\App;
use Slim\Middleware\ErrorMiddleware;
use Slim\Views\TwigMiddleware;
use Slim\Views\Twig;

return function (App $app) {
    $app->addBodyParsingMiddleware();
    $app->add(JsonBodyParserMiddleware::class);
    $app->addRoutingMiddleware();
    $app->add(TwigMiddleware::create($app, $app->getContainer()->get(Twig::class)));
    // $app->add(ErrorMiddleware::class);
    $app->add(ExceptionHandlerMiddleware::class);
    $app->add(new TrailingSlash());
};
