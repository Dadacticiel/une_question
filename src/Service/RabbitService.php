<?php
// src/Service/RabbitService.php
namespace App\Service;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Platformsh\ConfigReader\Config;
use Platformsh\ConfigReader\NotValidPlatformException;

class RabbitService
{
    private $connection;
    private $channel;

    /**
     * @param string $queue
     * @param string $exchange
     * @param string $routingKey
     * @return AMQPChannel
     */
    private function getChannel(string $queue = 'messages', $exchange = 'test_exchange', $routingKey = 'test_key') {
        try {
            // Create a new config object to ease reading the Platform.sh environment variables.
            // You can alternatively use getenv() yourself.
            $config = new Config();

            // Get the credentials to connect to the RabbitMQ service.
            $credentials = $config->credentials('rabbitmq');
        } catch(NotValidPlatformException $e) {
            $credentials = [
                'host' => '127.0.0.1',
                'port' => '30000',
                'username' => 'guest',
                'password' => 'guest'
            ];
        }

        try {
            // Connect to the RabbitMQ server.
            $this->connection = new AMQPStreamConnection($credentials['host'], $credentials['port'], $credentials['username'], $credentials['password']);
            $this->channel = $this->connection->channel();

            $this->channel->exchange_declare($exchange, 'direct', false, false, false);
            $this->channel->queue_declare($queue, false, true, false, false);
            $this->channel->queue_bind($queue, $exchange, $routingKey);

            return $this->channel;
        } catch (\Exception $e) {
            dd($e);
        }
    }

    public function publishMessage(string $body, $queue = 'messages', $exchange = 'test_exchange', $routingKey = 'test_key') {
        $channel = $this->getChannel($queue, $exchange, $routingKey);

        $msg = new AMQPMessage($body);
        $channel->basic_publish($msg, 'test_exchange', 'test_key');

        $this->channel->close();
        $this->channel->close();
    }

    public function consume($queue = 'messages', $exchange = 'test_exchange', $routingKey = 'test_key', $callback = null) {
        $channel = $this->getChannel($queue, $exchange, $routingKey);
        $channel->basic_consume('test_queue', '', false, true, false, false, $callback);

        while ($channel->is_consuming()) {
            $channel->wait();
        }

        $this->channel->close();
        $this->connection->close();
    }

    public function consumeAndReturn($queue = 'messages', $exchange = 'test_exchange', $routingKey = 'test_key', $callback = null) {
        $msg = null;
        $channel = $this->getChannel($queue, $exchange, $routingKey);
        $channel->basic_consume('test_queue', '', false, true, false, false, function($message) {
            $this->channel->close();
            $this->connection->close();
            $msg = $message;
        });

        while ($channel->is_consuming()) {
            $channel->wait();
        }

        return $msg->body;
    }
}