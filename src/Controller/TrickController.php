<?php

namespace App\Controller;

use App\Entity\Image;
use App\Entity\Trick;
use App\Entity\Videos;
use App\Helpers\Helpers;
use App\Repository\CommentsRepository;
use App\Repository\GroupRepository;
use App\Repository\TrickRepository;
use App\Services\TrickServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TrickController extends AbstractController
{

    /**
     * @var TrickRepository $trickRepository
     */
    private $trickRepository;

    /**
     * @var GroupRepository $groupRepository
     */
    private $groupRepository;

    /**
     * @var CommentsRepository $commentsRepository
     */
    private $commentsRepository;

    /**
     * @var Helpers $helpers
     */
    private $helpers;

    /**
     * @var EntityManagerInterface $entityManager
     */
    private $entityManager;

    /**
     * @var TrickServiceInterface $trickService
     */
    private $trickService;

    public function __construct(
        TrickRepository $trickRepository,
        CommentsRepository $commentsRepository,
        GroupRepository $groupRepository,
        EntityManagerInterface $entityManager,
        Helpers $helpers,
        TrickServiceInterface $trickService
    ) {
        $this->groupRepository = $groupRepository;
        $this->trickRepository = $trickRepository;
        $this->commentsRepository = $commentsRepository;
        $this->entityManager = $entityManager;
        $this->helpers = $helpers;
        $this->trickService = $trickService;
    }

    /**
     * @Route(
     *  "/admin/trick/list",
     *  name="trick_admin",
     *  methods={"GET"}
     * )
     * @return Response
     */
    public function admin(): Response
    {
        return $this->render('trick/admin.html.twig', [
            'tricks' => $this->trickRepository->findAll(),
        ]);
    }

    /**
     * @Route(
     *  "/trick/list",
     *  name="trick_index",
     *  methods={"GET"}
     * )
     * @return Response
     */
    public function index(Request $request): Response
    {
        $page = array_key_exists('page', $request->query->all()) ? (int) $request->query->get('page') : 0;
        $items_per_page = array_key_exists('items_per_page', $request->query->all()) ? (int) $request->query->get('items_per_page') : 10;

        return $this->render('trick/index.html.twig', [
            'tricks' => $this->trickRepository->paginate($page, $items_per_page),
            'pagination' => $this->helpers->pagination($this->trickRepository->findAll(), $items_per_page, (int) $page),
        ]);
    }

    /**
     * @Route(
     *  "/api/trick/{id}",
     *  requirements={"id"="\d+"},
     *  name="api_get_trick",
     *  methods={"GET"}
     * )
     * @param Trick $trick
     * @return JsonResponse
     */
    public function api_get_trick(Trick $trick): JsonResponse
    {
        return new JsonResponse([
            'trick' => $trick->serialize(), //($int $page = 0,$int $items_per_page = 10)
        ], Response::HTTP_OK);
    }

    /**
     * @Route(
     *  "/api/trick/pagination",
     *  name="api_trick_pagination",
     *  methods={"GET"}
     * )
     * @return JsonResponse
     */
    public function paginated_tricks(Request $request): JsonResponse
    {
        $page = array_key_exists('page', $request->query->all()) ? (int) $request->query->get('page') : 0;
        $items_per_page = array_key_exists('items_per_page', $request->query->all()) ? (int) $request->query->get('items_per_page') : 10;
        $response = $this->trickService->paginate($page, $items_per_page, $this->getUser());

        return new JsonResponse([
            'tricks' => $response->tricks,
            'last' => $response->last,
            'connected' => $response->connected,
        ], Response::HTTP_OK);
    }

    /**
     * @Route("/trick/new",
     *  name="trick_new",
     *  methods={"GET", "POST"}
     * )
     * @return Response
     */
    public function create(): Response
    {
        return $this->render('trick/new.html.twig', [
            'groups' => $this->groupRepository->findAll(),
        ]);
    }

    /**
     * @Route(
     *  "/admin/trick/{id}/edit",
     *  name="trick_edit",
     *  methods={"GET","POST"}
     * )
     * @param Trick $trick
     * @return Response
     */
    public function edit(Trick $trick): Response
    {
        return $this->render('trick/edit.html.twig', [
            'trick' => $trick,
            'groups' => $this->groupRepository->findAll(),
        ]);
    }

    /**
     * @Route(
     *  "/trick/{slug}/{id}",
     *  name="trick_show",
     *  requirements={"id"="\d+", "slug"="[\/A-Za-z0-9\-]+"},
     *  methods={"GET"}
     * )
     * @param Trick $trick
     * @param string $slug
     * @return Response
     */
    public function show(Trick $trick, string $slug): Response
    {
        if ($slug !== $trick->getSlug()) {
            return $this->redirectToRoute('trick_show', [
                'id' => $trick->getId(),
                'slug' => $trick->getSlug(),
            ], Response::HTTP_MOVED_PERMANENTLY);
        }

        return $this->render('trick/show.html.twig', [
            'trick' => $trick,
            'comments' => $this->commentsRepository->paginate($trick),
        ]);
    }

    /**
     * @Route(
     *  "/trick/{id}",
     *  name="trick_delete",
     *  methods={"DELETE"}
     * )
     * @param Request $request
     * @param Trick $trick
     * @return Response
     */
    public function delete(Trick $trick, Request $request): Response
    {
        if ($this->isCsrfTokenValid('delete' . $trick->getId(), $request->request->get('_token'))) {

            $this->trickService->deleteUploads($trick);

            $this->entityManager->remove($trick);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('trick_index');
    }

    /**
     * @Route(
     *  "/admin/admin/api/trick/store",
     *  name="api_trick_store",
     *  methods={"POST"}
     * )
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {

        if ($this->isCsrfTokenValid('create', $request->request->get('_token'))) {
            $data = $this->trickService->store($request);

            return new JsonResponse($data->response, $data->status);
        }

        return new JsonResponse([
            'message' => $this->helpers->setJsonMessage("Request failed"),
        ], Response::HTTP_BAD_REQUEST);
    }

    /**
     * @Route(
     *  "/admin/api/trick/edit/{id}",
     *  name="api_trick_edit",
     *  requirements={"id"="\d+"},
     *  methods={"POST"}
     * )
     * @param Trick $trick
     * @param Request $request
     * @return JsonResponse
     */
    public function editTrick(Trick $trick, Request $request): JsonResponse
    {
        $data = $this->trickService->editTrick($trick, $request);

        return new Jsonresponse($data->response, $data->status);
    }

    /**
     * @Route(
     *  "/admin/api/trick/edit/poster/{id}",
     *  name="api_trick_poster_edit",
     *  requirements={"id"="\d+"},
     *  methods={"POST"}
     * )
     * @param Trick $trick
     * @param Request $request
     * @return JsonResponse
     */
    public function editPoster(Trick $trick, Request $request): JsonResponse
    {
        $data = $this->trickService->updatePoster($request, $trick);

        return new JsonResponse($data->response, $data->status);
    }

    /**
     * @Route(
     *  "/admin/api/trick/edit/image/{id}",
     *  name="api_trick_image_edit",
     *  requirements={"id"="\d+"},
     *  methods={"POST"}
     * )
     * @param Image $image
     * @param Request $request
     * @return JsonResponse
     */
    public function editImage(Image $image, Request $request): JsonResponse
    {
        $data = $this->trickService->updateImage($request, $image);

        return new JsonResponse($data->response, $data->status);
    }

    /**
     * @Route(
     *  "/admin/api/trick/create/image/{id}",
     *  name="api_trick_image_create",
     *  requirements={"id"="\d+"},
     *  methods={"POST"}
     * )
     * @param Trick $trick
     * @param Request $request
     * @return JsonResponse
     */
    public function addImage(Trick $trick, Request $request): JsonResponse
    {
        $data = $this->trickService->addImage($request, $trick);

        return new JsonResponse($data->response, $data->status);
    }

    /**
     * @Route(
     *  "/admin/api/trick/create/video/{id}",
     *  name="api_trick_video_create",
     *  requirements={"id"="\d+"},
     *  methods={"POST"}
     * )
     * @param Trick $trick
     * @param Request $request
     * @return JsonResponse
     */
    public function addVideo(Trick $trick, Request $request): JsonResponse
    {
        $data = $this->trickService->addVideo($request, $trick);

        return new JsonResponse($data->response, $data->status);
    }

    /**
     * @Route(
     *  "/admin/api/trick/edit/video/{id}",
     *  name="api_trick_video_edit",
     *  requirements={"id"="\d+"},
     *  methods={"POST"}
     * )
     * @param Videos $video
     * @param Request $request
     * @return JsonResponse
     */
    public function editVideo(Videos $video, Request $request): JsonResponse
    {
        $data = $this->trickService->updateVideo($request, $video);

        return new JsonResponse($data->response, $data->status);
    }

    /**
     * @Route(
     *  "/admin/api/trick/delete/image/{id}",
     *  name="api_trick_image_delete",
     *  requirements={"id"="\d+"},
     *  methods={"DELETE"}
     * )
     * @param Image $image
     * @return JsonResponse
     */
    public function deleteImage(Image $image): JsonResponse
    {
        $data = $this->trickService->deleteImage($image);

        return new JsonResponse($data->response, $data->status);
    }

    /**
     * @Route(
     *  "/admin/api/trick/delete/video/{id}",
     *  name="api_trick_video_delete",
     *  requirements={"id"="\d+"},
     *  methods={"DELETE"}
     * )
     * @param Videos $video
     * @return JsonResponse
     */
    public function deleteVideo(Videos $video): JsonResponse
    {
        $data = $this->trickService->deleteVideo($video);

        return new JsonResponse($data->response, $data->status);
    }

    /**
     * @Route(
     *  "/admin/api/trick/delete/poster/{id}",
     *  name="api_trick_poster_delete",
     *  requirements={"id"="\d+"},
     *  methods={"DELETE"}
     * )
     * @param Trick $trick
     * @return JsonResponse
     */
    public function deletePoster(Trick $trick): JsonResponse
    {
        $data = $this->trickService->deletePoster($trick);

        return new JsonResponse($data->response, $data->status);
    }

}
