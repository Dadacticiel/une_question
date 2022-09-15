<?php
// src/Controller/RabbitController.php
namespace App\Controller;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Platformsh\ConfigReader\Config;
use Platformsh\ConfigReader\NotValidPlatformException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RabbitController
{
    #[Route('/rabbit', name: 'rabbit')]
    public function rabbit(): Response
    {
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
                'password' => 'guest',
            ];
            $credentials = [
                'host' => 'localhost',
                'port' => '15672',
                'username' => 'guest',
                'password' => 'guest',
            ];
        }

        dd($credentials);

        try {

            $queueName = 'deploy_days';

            // Connect to the RabbitMQ server.
            $connection = new AMQPStreamConnection($credentials['host'], $credentials['port'], $credentials['username'], $credentials['password']);
            $channel = $connection->channel();

            $channel->queue_declare($queueName, false, false, false, false);

            $msg = new AMQPMessage('Friday');
            $channel->basic_publish($msg, '', 'hello');

            echo "[x] Sent 'Friday'<br/>\n";

            // In a real application you't put the following in a separate script in a loop.
            $callback = function ($msg) {
                printf("[x] Deploying on %s<br />\n", $msg->body);
            };

            $channel->basic_consume($queueName, '', false, true, false, false, $callback);

            // This blocks on waiting for an item from the queue, so comment it out in this demo script.
            //$channel->wait();

            $channel->close();
            $connection->close();

        } catch (\Exception $e) {
            dd($e);
        }

        return new Response('YEAY');
    }
}