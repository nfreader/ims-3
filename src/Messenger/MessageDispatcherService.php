<?php

namespace App\Messenger;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class MessageDispatcherService
{
    private AMQPStreamConnection $connection;
    private AMQPChannel $channel;

    public const NOTIFICATION_EXCHANGE = 'topic_notifications';

    public function __construct(
        string $host,
        int $port,
        string $username,
        string $password
    ) {
        $this->connection = new AMQPStreamConnection(
            $host,
            $port,
            $username,
            $password
        );
        $this->channel = $this->connection->channel();
        $this->channel->exchange_declare(
            self::NOTIFICATION_EXCHANGE,
            'topic',
            false,
            false,
            false
        );
    }

    public function publishMessage(mixed $data, string $routing_key): void
    {
        $this->channel->basic_publish(
            new AMQPMessage(serialize($data)),
            self::NOTIFICATION_EXCHANGE,
            $routing_key
        );
    }

    public static function newFromDSN(string $dsn): self
    {
        $settings = parse_url($dsn);
        return new self(
            $settings['host'],
            $settings['port'],
            $settings['user'],
            $settings['pass']
        );
    }

}
