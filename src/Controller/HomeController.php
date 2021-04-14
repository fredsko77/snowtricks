<?php

namespace App\Controller;

use App\Services\TrickServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{

    /**
     * @var TrickServiceInterface $trickService
     */
    private $trickService;

    public function __construct(TrickServiceInterface $trickService)
    {
        $this->trickService = $trickService;
    }
    /**
     * @Route("/", name="home")
     */
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
            'title' => 'Page d\' accueil ',
            'tricks' => $this->trickService->paginate(0, 10, $this->getUser())->tricks,
        ]);
    }
}
