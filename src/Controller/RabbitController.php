<?php
// src/Controller/RabbitController.php
namespace App\Controller;

use App\Service\RabbitService;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RabbitController
{
    #[Route('/rabbit/{message}', name: 'rabbit')]
    public function rabbit(string $message, RabbitService $rabbitService): Response
    {
        $rabbitService->publishMessage($message ?? 'Salut toi');

        return new Response('Message envoyé !');
    }

    #[Route('/consume', name: 'consume')]
    public function consume(RabbitService $rabbitService, LoggerInterface $logger): Response
    {
        $test = '';
        // In a real application you't put the following in a separate script in a loop.
        $callback = function ($msg) use ($logger) {
            $logger->info('Message received : ' . $msg->body);
            $test = $msg->body;
        };

        $rabbitService->consume(callback: $callback);
    }

    #[Route('/get-new-messages', name: 'get-new-messages')]
    public function getNewMessages(RabbitService $rabbitService, LoggerInterface $logger): Response
    {
        return new Response(json_encode($rabbitService->getNewMessages()));
    }

    #[Route('/get-old-messages', name: 'get-old-messages')]
    public function getOldMessages(RabbitService $rabbitService, LoggerInterface $logger): Response
    {
        return new Response(json_encode($rabbitService->getOldMessages()));
    }
}