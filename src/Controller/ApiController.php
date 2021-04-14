<?php

namespace App\Controller;

use App\Services\TrickServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ApiController extends AbstractController
{

    /**
     * @var TrickServiceInterface $trickService
     */
    private $trickService;

    public function __construct(TrickServiceInterface $trickService)
    {
        $this->trickService = $trickService;
    }

    public function paginate(): JsonResponse
    {
        // TODO $trickService paginate
        return new JsonResponse([], Response::HTTP_OK);
    }
}
