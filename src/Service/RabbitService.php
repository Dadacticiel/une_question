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
    private $channel;
    private $messages = [];

    public function __construct(private readonly LoggerInterface $logger) {

    }

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
        $channel->basic_consume($queue, '', false, true, false, false, $callback);


        $channel->wait();

        $this->channel->close();
        $this->connection->close();
    }

    /**
     * Récupération de tous les messages présents dans la queue "messages_read" et on les réinsère dans la queue
     * @param $queue
     * @param $exchange
     * @param $routingKey
     * @param $requeue
     * @return array
     */
    public function getOldMessages($queue = 'messages_read', $exchange = 'test_exchange', $routingKey = 'test_key', $requeue = true): array
    {
        $channel = $this->getChannel($queue, $exchange, $routingKey);

        list($queueName, $messageCount, $consumerCount) = $channel->queue_declare($queue, true);

        $callback = function ($msg) use ($channel, $queue, $exchange, $routingKey, $requeue) {
            /** @var AMQPMessage $msg */
            $this->messages[] = $msg;

            // On ne valide pas la lecture du message et on le remet dans la queue
            if($requeue) {
                $msg->getChannel()->basic_nack($msg->getDeliveryTag(), false, true);
            } else {
                $msg->getChannel()->basic_ack($msg->getDeliveryTag());
            }
        };

        for($i = 0; $i < $messageCount; $i++) {
            $channel->basic_consume($queue, '', false, false, false, false, $callback);
            $channel->wait();
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
    public function getNewMessages($queue = 'messages', $exchange = 'test_exchange', $routingKey = 'test_key') {
        $channel = $this->getChannel($queue, $exchange, $routingKey);

        list($queueName, $messageCount, $consumerCount) = $channel->queue_declare($queue, true);

        $callback = function ($msg) use ($channel, $queue, $exchange, $routingKey) {
            /** @var AMQPMessage $msg */
             $this->messages[] = $msg;
             $this->logger->info('Message reçu : ' . $msg->body);

//            // On ajoute ce message à la liste des messages lus
//            $channelRead = $this->getChannel('messages_read', $exchange, $routingKey);
//            $channelRead->basic_publish($msg, $exchange, $routingKey);

            // On valide la lecture du message, on le supprime de la queue "messages"
            $msg->getChannel()->basic_ack($msg->getDeliveryTag());
        };

        // Si pas de message dans la queue, on va en attendre 1 nouveau
        if($messageCount === 0) {
            $messageCount = 1;
        }

        // On va attendre d'avoir consommer tous les messages de la queue. Si aucun message n'est dans la queue, on va en attendre qu'un nouveau soit inséré
        for($i = 0; $i < $messageCount; $i++) {
            $channel->basic_consume($queue, '', false, false, false, false, $callback);
            $channel->wait();
        }

        $channel->getConnection()->close();
        $channel->close();

        return $this->messages;
    }
}