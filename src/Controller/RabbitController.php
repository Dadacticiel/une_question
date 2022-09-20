<?php
// src/Controller/RabbitController.php
namespace App\Controller;

use App\Service\RabbitService;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RabbitController
{
    #[Route('/publish', name: 'publish')]
    public function publish(string $message, RabbitService $rabbitService, Request $request): Response
    {
        $message = $request->request->get('message');
        $rabbitService->publishMessage($message ?? "C'est vide !");

        return new Response('Message envoyé !');
    }

    #[Route('/clear', name: 'clear')]
    public function consume(RabbitService $rabbitService, LoggerInterface $logger): Response
    {
        return new Response(json_encode($rabbitService->getOldMessages(requeue: false)));
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