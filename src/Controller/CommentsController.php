<?php

namespace App\Controller;

use App\Entity\Comments;
use App\Entity\Trick;
use App\Services\CommentsServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommentsController extends AbstractController
{

    /**
     * @var CommentsServiceInterface $commentsService
     */
    private $commentsService;

    public function __construct(CommentsServiceInterface $commentsService)
    {
        $this->commentsService = $commentsService;
    }

    /**
     * @Route(
     *  "/comments",
     *  name="comments"
     * )
     */
    public function index(): Response
    {
        return $this->render('comments/index.html.twig', [
            'controller_name' => 'CommentsController',
        ]);
    }

    /**
     * @Route(
     *  "/api/comments/{id}",
     *  name="api_comment_page",
     *  requirements={"id"="\d+"},
     *  methods={"GET"}
     * )
     * @return JsonResponse
     */
    public function paginated_comments(Trick $trick, Request $request): JsonResponse
    {
        $page = array_key_exists('page', $request->query->all()) ? (int) $request->query->get('page') : 0;
        $items_per_page = array_key_exists('items_per_page', $request->query->all()) ? (int) $request->query->get('items_per_page') : 5;
        $response = $this->commentsService->paginate($trick, $this->getUser(), $page, $items_per_page);

        return new JsonResponse([
            'comments' => $response->comments,
            'last' => $response->last,
            'connected' => $response->connected,
        ], Response::HTTP_OK);
    }

    /**
     * @Route(
     *  "/admin/comments/{id}/store",
     *  name="api_comments_store",
     *  requirements={"id":"\d+"},
     *  methods={"POST"}
     * )
     */
    public function store(Trick $trick, Request $request): JsonResponse
    {
        $data = $this->commentsService->store($request, $trick, $this->getUser());

        return new JsonResponse($data->response, $data->status);
    }

    /**
     * @Route(
     *  "/admin/comments/delete/{id}",
     *  name="api_comments_delete",
     *  requirements={"id":"\d+"},
     *  methods={"DELETE"}
     * )
     */
    public function delete(Comments $comment): JsonResponse
    {
        $data = $this->commentsService->delete($comment);

        return new JsonResponse($data->response, $data->status);
    }

}
