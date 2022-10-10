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
        $rabbitService->publishMessage($message ?? "C'est vide !", 'messages');

        return new Response('Message envoyé !');
    }

    #[Route('/applause', name: 'applause')]
    public function applause(RabbitService $rabbitService, Request $request): Response
    {
        $rabbitService->publishMessage(body: "clap", exchange: "reactions", routingKey: "clap");

        return new Response('Clap envoyé !');
    }

    #[Route('/heart', name: 'heart')]
    public function heart(RabbitService $rabbitService, Request $request): Response
    {
        $rabbitService->publishMessage(body: "heart", exchange: "reactions", routingKey: "heart");

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


    #[Route('/mad', name: 'mad')]
    public function mad(RabbitService $rabbitService, Request $request): Response
    {
        $params = json_decode($request->getContent(), true);
        $rabbitService->publishMessage("Merci, c'était super !", 'messages');
        $rabbitService->publishMessage(body: "clap", exchange: "reactions", routingKey: "clap");
        $rabbitService->publishMessage(body: "clap", exchange: "reactions", routingKey: "clap");
        $rabbitService->publishMessage(body: "heart", exchange: "reactions", routingKey: "heart");

        usleep(100);
        $rabbitService->publishMessage("Merci", 'messages');
        $rabbitService->publishMessage(body: "heart", exchange: "reactions", routingKey: "heart");
        $rabbitService->publishMessage(body: "heart", exchange: "reactions", routingKey: "heart");
        $rabbitService->publishMessage(body: "heart", exchange: "reactions", routingKey: "heart");
        $rabbitService->publishMessage(body: "clap", exchange: "reactions", routingKey: "clap");
        $rabbitService->publishMessage(body: "clap", exchange: "reactions", routingKey: "clap");
        $rabbitService->publishMessage(body: "clap", exchange: "reactions", routingKey: "clap");

        usleep(100);
        $rabbitService->publishMessage("Bravo", 'messages');
        $rabbitService->publishMessage(body: "heart", exchange: "reactions", routingKey: "heart");
        $rabbitService->publishMessage(body: "heart", exchange: "reactions", routingKey: "heart");
        $rabbitService->publishMessage(body: "heart", exchange: "reactions", routingKey: "heart");
        $rabbitService->publishMessage(body: "clap", exchange: "reactions", routingKey: "clap");
        $rabbitService->publishMessage(body: "clap", exchange: "reactions", routingKey: "clap");
        $rabbitService->publishMessage(body: "clap", exchange: "reactions", routingKey: "clap");


        usleep(100);
        $rabbitService->publishMessage("Super présentation", 'messages');
        $rabbitService->publishMessage(body: "heart", exchange: "reactions", routingKey: "heart");
        $rabbitService->publishMessage(body: "heart", exchange: "reactions", routingKey: "heart");
        $rabbitService->publishMessage(body: "heart", exchange: "reactions", routingKey: "heart");
        $rabbitService->publishMessage(body: "clap", exchange: "reactions", routingKey: "clap");
        $rabbitService->publishMessage(body: "clap", exchange: "reactions", routingKey: "clap");
        $rabbitService->publishMessage(body: "clap", exchange: "reactions", routingKey: "clap");

        usleep(100);
        $rabbitService->publishMessage("Meilleur sujet du meetup !", 'messages');
        $rabbitService->publishMessage(body: "heart", exchange: "reactions", routingKey: "heart");
        $rabbitService->publishMessage(body: "heart", exchange: "reactions", routingKey: "heart");
        $rabbitService->publishMessage(body: "heart", exchange: "reactions", routingKey: "heart");
        $rabbitService->publishMessage(body: "clap", exchange: "reactions", routingKey: "clap");
        $rabbitService->publishMessage(body: "clap", exchange: "reactions", routingKey: "clap");
        $rabbitService->publishMessage(body: "clap", exchange: "reactions", routingKey: "clap");
        $rabbitService->publishMessage(body: "heart", exchange: "reactions", routingKey: "heart");
        $rabbitService->publishMessage(body: "heart", exchange: "reactions", routingKey: "heart");
        $rabbitService->publishMessage(body: "heart", exchange: "reactions", routingKey: "heart");
        $rabbitService->publishMessage(body: "clap", exchange: "reactions", routingKey: "clap");
        $rabbitService->publishMessage(body: "clap", exchange: "reactions", routingKey: "clap");
        $rabbitService->publishMessage(body: "clap", exchange: "reactions", routingKey: "clap");

        usleep(100);
        $rabbitService->publishMessage("On veut plus de télétravail !", 'messages');
        $rabbitService->publishMessage(body: "heart", exchange: "reactions", routingKey: "heart");
        $rabbitService->publishMessage(body: "heart", exchange: "reactions", routingKey: "heart");
        $rabbitService->publishMessage(body: "heart", exchange: "reactions", routingKey: "heart");
        $rabbitService->publishMessage(body: "clap", exchange: "reactions", routingKey: "clap");
        $rabbitService->publishMessage(body: "clap", exchange: "reactions", routingKey: "clap");
        $rabbitService->publishMessage(body: "clap", exchange: "reactions", routingKey: "clap");

        usleep(100);
        $rabbitService->publishMessage("Super, vous devriez être sponsorisés par Platform.sh", 'messages');
        $rabbitService->publishMessage(body: "heart", exchange: "reactions", routingKey: "heart");
        $rabbitService->publishMessage(body: "heart", exchange: "reactions", routingKey: "heart");
        $rabbitService->publishMessage(body: "heart", exchange: "reactions", routingKey: "heart");
        $rabbitService->publishMessage(body: "clap", exchange: "reactions", routingKey: "clap");
        $rabbitService->publishMessage(body: "clap", exchange: "reactions", routingKey: "clap");
        $rabbitService->publishMessage(body: "clap", exchange: "reactions", routingKey: "clap");

        usleep(100);
        $rabbitService->publishMessage("Gloire aux devs !", 'messages');
        $rabbitService->publishMessage(body: "heart", exchange: "reactions", routingKey: "heart");
        $rabbitService->publishMessage(body: "heart", exchange: "reactions", routingKey: "heart");
        $rabbitService->publishMessage(body: "heart", exchange: "reactions", routingKey: "heart");
        $rabbitService->publishMessage(body: "clap", exchange: "reactions", routingKey: "clap");
        $rabbitService->publishMessage(body: "clap", exchange: "reactions", routingKey: "clap");
        $rabbitService->publishMessage(body: "clap", exchange: "reactions", routingKey: "clap");

        usleep(100);
        $rabbitService->publishMessage("Êtes-vous disponibles pour rejoindre nos équipes ??", 'messages');
        $rabbitService->publishMessage(body: "heart", exchange: "reactions", routingKey: "heart");
        $rabbitService->publishMessage(body: "heart", exchange: "reactions", routingKey: "heart");
        $rabbitService->publishMessage(body: "heart", exchange: "reactions", routingKey: "heart");
        $rabbitService->publishMessage(body: "clap", exchange: "reactions", routingKey: "clap");
        $rabbitService->publishMessage(body: "clap", exchange: "reactions", routingKey: "clap");
        $rabbitService->publishMessage(body: "clap", exchange: "reactions", routingKey: "clap");

        usleep(100);
        $rabbitService->publishMessage("Brendan, donne moi ton 06 !", 'messages');
        $rabbitService->publishMessage(body: "heart", exchange: "reactions", routingKey: "heart");
        $rabbitService->publishMessage(body: "heart", exchange: "reactions", routingKey: "heart");
        $rabbitService->publishMessage(body: "heart", exchange: "reactions", routingKey: "heart");
        $rabbitService->publishMessage(body: "clap", exchange: "reactions", routingKey: "clap");
        $rabbitService->publishMessage(body: "clap", exchange: "reactions", routingKey: "clap");
        $rabbitService->publishMessage(body: "clap", exchange: "reactions", routingKey: "clap");

        usleep(100);
        $rabbitService->publishMessage("C'était top", 'messages');
        $rabbitService->publishMessage(body: "heart", exchange: "reactions", routingKey: "heart");
        $rabbitService->publishMessage(body: "heart", exchange: "reactions", routingKey: "heart");
        $rabbitService->publishMessage(body: "heart", exchange: "reactions", routingKey: "heart");
        $rabbitService->publishMessage(body: "clap", exchange: "reactions", routingKey: "clap");
        $rabbitService->publishMessage(body: "clap", exchange: "reactions", routingKey: "clap");
        $rabbitService->publishMessage(body: "clap", exchange: "reactions", routingKey: "clap");
        $rabbitService->publishMessage(body: "heart", exchange: "reactions", routingKey: "heart");
        $rabbitService->publishMessage(body: "heart", exchange: "reactions", routingKey: "heart");
        $rabbitService->publishMessage(body: "heart", exchange: "reactions", routingKey: "heart");
        $rabbitService->publishMessage(body: "clap", exchange: "reactions", routingKey: "clap");
        $rabbitService->publishMessage(body: "clap", exchange: "reactions", routingKey: "clap");
        $rabbitService->publishMessage(body: "clap", exchange: "reactions", routingKey: "clap");
        $rabbitService->publishMessage(body: "heart", exchange: "reactions", routingKey: "heart");
        $rabbitService->publishMessage(body: "heart", exchange: "reactions", routingKey: "heart");
        $rabbitService->publishMessage(body: "heart", exchange: "reactions", routingKey: "heart");
        $rabbitService->publishMessage(body: "clap", exchange: "reactions", routingKey: "clap");
        $rabbitService->publishMessage(body: "clap", exchange: "reactions", routingKey: "clap");
        $rabbitService->publishMessage(body: "clap", exchange: "reactions", routingKey: "clap");

        return new Response('Message envoyé !');
    }
}