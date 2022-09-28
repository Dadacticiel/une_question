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
    public function publish(RabbitService $rabbitService, Request $request): Response
    {
        $params = json_decode($request->getContent(), true);
        $message = $params['message'];
        $rabbitService->publishMessage($message ?? "C'est vide !");

        return new Response('Message envoyé !');
    }

    #[Route('/applause', name: 'applause')]
    public function applause(RabbitService $rabbitService, Request $request): Response
    {
        $rabbitService->publishMessage(body: "applause", queue: "applause", exchange: "reactions");

        return new Response('Applause envoyé !');
    }

    #[Route('/heart', name: 'heart')]
    public function heart(RabbitService $rabbitService, Request $request): Response
    {
        $rabbitService->publishMessage(body: "heart", queue: "heart", exchange: "reactions");

        return new Response('Heart envoyé !');
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