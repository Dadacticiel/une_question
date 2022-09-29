<?php
// src/Controller/MainController.php
namespace App\Controller;

use App\Service\RabbitService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'index')]
    #[Route('/questions', name: 'questions')]
    public function index(RabbitService $rabbitService): Response
    {
        $rabbitService->initEchangesAndQueues();

        return $this->render('base.html.twig');
    }

    #[Route('/lucky/number/{max}', name: 'app_lucky_number')]
    public function number(int $max): Response
    {
        $number = random_int(0, $max);

        return new Response(
            '<html><body>Lucky number: ' . $number . '</body></html>'
        );
    }
}