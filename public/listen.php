<?php

use App\Consumer\EmailNotificationConsumer;
use PhpAmqpLib\Connection\AMQPStreamConnection;

require_once __DIR__."/../vendor/autoload.php";

$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();

$channel->exchange_declare('topic_notifications', 'topic', false, false, false);

list($queue_name, , ) = $channel->queue_declare("", false, false, true, false);

$binding_keys = ['email.#'];

foreach ($binding_keys as $binding_key) {
    $channel->queue_bind($queue_name, 'topic_notifications', $binding_key);
}

echo " [*] Waiting for logs. To exit press CTRL+C\n";

$callback = function ($msg) {
    echo ' [x] ', $msg->getRoutingKey(), ':', $msg->getBody(), "\n";
    $consumer = new EmailNotificationConsumer();
    $consumer->consume($msg);
};

$channel->basic_consume($queue_name, '', false, true, false, false, $callback);

try {
    $channel->consume();
} catch (\Throwable $exception) {
    echo $exception->getMessage();
}

$channel->close();
$connection->close();
