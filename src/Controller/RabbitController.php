<?php
// src/Controller/RabbitController.php
namespace App\Controller;

use App\Service\RabbitService;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RabbitController
{
    #[Route('/rabbit', name: 'rabbit')]
    public function rabbit(RabbitService $rabbitService): Response
    {
        $rabbitService->publishMessage('Salut toi');

        return new Response('Message envoyé !');
    }

    #[Route('/consume', name: 'consume')]
    public function consume(RabbitService $rabbitService, LoggerInterface $logger): Response
    {
        // In a real application you't put the following in a separate script in a loop.
        $callback = function ($msg) use ($logger) {
                $logger->info('Message received : ' . $msg->body);
        };

        $rabbitService->consume(callback: $callback);

        return new Response('');
    }

    #[Route('/consume-and-return', name: 'consume-and-return')]
    public function consumeAndReturn(RabbitService $rabbitService, LoggerInterface $logger): Response
    {
        // In a real application you't put the following in a separate script in a loop.
        $callback = function ($msg) use ($logger) {
                $logger->info('Message received : ' . $msg->body);
        };

        return new Response($rabbitService->consumeAndReturn(callback: $callback));
    }
}