<?php
// src/Service/RabbitService.php
namespace App\Service;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Platformsh\ConfigReader\Config;
use Platformsh\ConfigReader\NotValidPlatformException;
use Psr\Log\LoggerInterface;

class RabbitService
{
    private $connection;
    /** @var AMQPChannel $channel */
    private $channel;
    private $messages = [];

    public function __construct(private readonly LoggerInterface $logger) {
    }

    public function initEchangesAndQueues() {
        $this->getChannel();

        // Create exchanges
        $this->channel->exchange_declare('messages', 'direct', false, false, false);
        $this->channel->exchange_declare('reactions', 'direct', false, false, false);
        // Create the queue
        $this->channel->queue_declare('messages_unread', false, true, false, false);
        $this->channel->queue_declare('messages_read', false, true, false, false);
        $this->channel->queue_declare('claps', false, true, false, false);
        $this->channel->queue_declare('hearts', false, true, false, false);
        // Link the queue to the exchange
        $this->channel->queue_bind('messages_unread', 'messages');
        $this->channel->queue_bind('messages_read', 'messages');
        $this->channel->queue_bind('claps', 'reactions', 'clap');
        $this->channel->queue_bind('hearts', 'reactions', 'heart');
    }

    /**
     * @param string $queue
     * @param string $exchange
     * @param string $routingKey
     * @return AMQPChannel
     */
    private function getChannel()
    {
        if(!empty($this->channel)) {
            return $this->channel;
        }

        try {
            // Create a new config object to ease reading the Platform.sh environment variables.
            // You can alternatively use getenv() yourself.
            $config = new Config();

            // Get the credentials to connect to the RabbitMQ service.
            $credentials = $config->credentials('rabbitmq');
        } catch (NotValidPlatformException $e) {
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

            return $this->channel;
        } catch (\Exception $e) {
            dd($e);
        }
    }

    public function publishMessage(string $body, $exchange, $routingKey = '')
    {
        $channel = $this->getChannel();

        $msg = new AMQPMessage($body);
        $channel->basic_publish($msg, $exchange, $routingKey);

        $this->channel->close();
    }

    public function consume($queue = 'messages', $exchange = 'test_exchange', $routingKey = '', $callback = null)
    {
        $channel = $this->getChannel();
        $channel->basic_consume($queue, '', false, true, false, false, $callback);


        $channel->wait(timeout: 10);

        $this->channel->close();
        $this->connection->close();
    }

    /**
     * Récupération de tous les messages présents dans la queue "messages_read" et on les réinsère dans la queue
     * @param bool $requeue
     * @return array
     */
    public function getOldMessages(bool $requeue = true): array
    {
        $channel = $this->getChannel();

        list($queueName, $messageCount, $consumerCount) = $channel->queue_declare("messages_read", true);

        $callback = function ($msg) use ($channel, $requeue) {
            /** @var AMQPMessage $msg */
            $this->messages[] = $msg;

            // On ne valide pas la lecture du message et on le remet dans la queue
            if ($requeue) {
                $msg->getChannel()->basic_nack($msg->getDeliveryTag(), false, true);
            } else {
                $msg->getChannel()->basic_ack($msg->getDeliveryTag());
            }
        };

        for ($i = 0; $i < $messageCount; $i++) {
            $channel->basic_consume("messages_read", '', false, false, false, false, $callback);
            $channel->wait(timeout: 10);
        }

        $channel->getConnection()->close();
        $channel->close();

        return $this->messages;
    }

    /**
     * On va écouter consommer les nouveaux messages de la queue "messages". Va attendre si pas de nouveau message de disponible
     * @param $queue
     * @param $exchange
     * @param $routingKey
     * @param $callback
     * @return array
     */
    public function getNewMessages()
    {
        $channel = $this->getChannel();

        list($queueName, $messageCount, $consumerCount) = $channel->queue_declare("messages_unread", true);
        list($queueName, $heartCount, $consumerCount) = $channel->queue_declare("hearts", true);
        list($queueName, $clapCount, $consumerCount) = $channel->queue_declare("claps", true);
        // The number of messages we got to retrieve
        $totalCount = $messageCount + $heartCount + $clapCount;

        $callback = function ($msg) use ($channel) {
            /** @var AMQPMessage $msg */
            $this->messages[] = $msg;
            $this->logger->info('Message reçu : ' . $msg->body);

            // On valide la lecture du message, on le supprime de la queue "messages"
            $msg->getChannel()->basic_ack($msg->getDeliveryTag());
        };

        // Si pas de message dans la queue, on va en attendre 1 nouveau
        if ($totalCount === 0) {
            $totalCount = 1;
        }

        // On va attendre d'avoir consommer tous les messages de la queue. Si aucun message n'est dans la queue, on va en attendre qu'un nouveau soit inséré
        for ($i = 0; $i < $totalCount; $i++) {
            $channel->basic_consume("messages_unread", '', false, false, false, false, $callback);
            $channel->basic_consume("claps", '', false, false, false, false, $callback);
            $channel->basic_consume("hearts", '', false, false, false, false, $callback);
            $channel->wait(timeout: 10);
        }

        $channel->getConnection()->close();
        $channel->close();

        return $this->messages;
    }
}