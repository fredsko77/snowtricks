<?php
namespace App\Services;

use App\Entity\Image;
use App\Entity\Trick;
use App\Entity\User;
use App\Entity\Videos;
use App\Helpers\Helpers;
use App\Repository\GroupRepository;
use App\Repository\ImageRepository;
use App\Repository\TrickRepository;
use App\Services\TrickServiceInterface;
use App\Services\UploadService;
use Cocur\Slugify\Slugify;
use Doctrine\ORM\EntityManagerInterface;
use stdClass;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Security;

class TrickService implements TrickServiceInterface
{

    /**
     * @var Helpers $helpers
     */
    private $helpers;

    /**
     * @var TrickRepository $trickRepository
     */
    private $trickRepository;

    /**
     * @var EntityManagerInterface $entityManager
     */
    private $entityManager;

    /**
     * @var ImageRepository $imageRepository
     */
    private $imageRepository;

    /**
     * @var GroupRepository $groupRepository
     */
    private $groupRepository;

    public function __construct(
        Helpers $helpers,
        TrickRepository $trickRepository,
        EntityManagerInterface $entityManager,
        ImageRepository $imageRepository,
        GroupRepository $groupRepository,
        UploadService $uploadService,
        Security $security,
        RouterInterface $router
    ) {
        $this->helpers = $helpers;
        $this->trickRepository = $trickRepository;
        $this->entityManager = $entityManager;
        $this->imageRepository = $imageRepository;
        $this->groupRepository = $groupRepository;
        $this->uploadService = $uploadService;
        $this->security = $security;
        $this->router = $router;
    }

    /**
     * {@inheritdoc}
     */
    public function store(Request $request): object
    {
        $data = new stdClass;
        $data->response['message'] = $this->helpers->setJsonMessage('No content');
        $data->status = Response::HTTP_NO_CONTENT;
        $trick = new Trick();
        $poster = '';

        if ($this->helpers->isFilled($request->request->all())) {

            if ($request->files->get('poster') instanceof UploadedFile) {
                $poster = $this->uploadService->upload($request->files->get('poster'));
            }

            $trick->setDescription($request->request->get('description'))
                ->setName($request->request->get('name'))
                ->setPoster($poster)
                ->setGroup($this->groupRepository->find((int) $request->request->get('group')))
                ->setCreatedAt($this->helpers->now())
                ->setSlug((new Slugify)->slugify($request->request->get('name')))
                ->setUser($this->security->getUser())
            ;

            $this->entityManager->persist($trick);
            $this->entityManager->flush();

            if (count($request->request->get('videos')) > 0) {
                foreach ($request->request->get('videos') as $videos) {
                    if ($videos !== "") {
                        $video = new Videos();
                        $video->setUrl($this->helpers->videoFormatURL($videos))
                            ->setTrick($trick)
                            ->setCreatedAt($this->helpers->now())
                        ;

                        $this->entityManager->persist($video);
                        $this->entityManager->flush();
                    }
                }
            }

            if (count($request->files->get('images')) > 0) {
                foreach ($request->files->get('images') as $images) {
                    $image = new Image();
                    $upload = $this->uploadService->upload($images);

                    $image->setPath($upload)
                        ->setTrick($trick)
                        ->setType('trick_image')
                        ->setCreatedAt($this->helpers->now())
                    ;

                    $this->entityManager->persist($image);
                    $this->entityManager->flush();
                }
            }

            $data->response['message'] = $this->helpers->setJsonMessage('Trick created', 'success');
            $data->response['url'] = $this->router->generate('trick_index');
            $data->status = Response::HTTP_CREATED;
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function updatePoster(Request $request, Trick $trick): object
    {
        $data = new stdClass;
        $data->response = [];

        $data->response['message'] = $this->helpers->setJsonMessage('No file received');
        $data->status = Response::HTTP_NO_CONTENT;

        $uploadedFile = $request->files->get('poster');

        if ($uploadedFile instanceof UploadedFile) {

            $upload = $this->uploadService->upload($uploadedFile);

            if (is_array($upload)) {
                $data->response['errors'] = $upload;
                $data->status = Response::HTTP_BAD_REQUEST;

                return $data;
            }

            $oldImage = $trick->getPoster();

            if (is_file($oldImage)) {
                unlink($oldImage);
            }

            $trick->setPoster($upload);
            $this->entityManager->persist($trick);
            $this->entityManager->flush();

            $data->response['message'] = $this->helpers->setJsonMessage('File uploaded', 'success');
            $data->response['uploadedFile'] = $upload;
            $data->status = Response::HTTP_CREATED;
            return $data;
        }

        return $data;

    }

    /**
     * {@inheritdoc}
     */
    public function updateImage(Request $request, Image $image): object
    {

        $data = new stdClass;
        $data->response = [];

        $data->response['message'] = $this->helpers->setJsonMessage('No file received !');
        $data->status = Response::HTTP_NO_CONTENT;

        $uploadedFile = $request->files->get('file');

        if ($uploadedFile instanceof UploadedFile) {

            $upload = $this->uploadService->upload($uploadedFile);

            if (is_array($upload)) {
                $data->response['errors'] = $upload;
                $data->status = Response::HTTP_BAD_REQUEST;

                return $data;
            }

            $oldImage = $image->getPath();

            if (is_file($oldImage)) {
                unlink($oldImage);
            }

            $image->setPath($upload);
            $this->entityManager->persist($image);
            $this->entityManager->flush();

            $data->response['message'] = $this->helpers->setJsonMessage('File uploaded 🚀', 'success');
            $data->response['uploadedFile'] = $upload;
            $data->status = Response::HTTP_CREATED;
            return $data;
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function addImage(Request $request, Trick $trick): object
    {
        $image = new Image();
        $data = new stdClass;

        $data->status = Response::HTTP_NO_CONTENT;
        $data->response = [];
        $data->response['message'] = $this->helpers->setJsonMessage('No file received !');

        $uploadedFile = $request->files->get('file');

        if ($uploadedFile instanceof UploadedFile) {
            $upload = $this->uploadService->upload($uploadedFile);

            if (is_array($upload)) {
                $data->response['errors'] = $upload;
                $data->status = Response::HTTP_BAD_REQUEST;

                return $data;
            }

            $image->setPath($upload)
                ->setType('trick_image')
                ->setTrick($trick)
                ->setCreatedAt($this->helpers->now())
            ;

            $this->entityManager->persist($image);
            $this->entityManager->flush();

            $data->response['image'] = $image->serialize();
            $data->response['message'] = $this->helpers->setJsonMessage('File uploaded 🚀', 'success');
            $data->status = Response::HTTP_CREATED;
            return $data;
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function addVideo(Request $request, Trick $trick): object
    {
        $data = new stdClass;
        $data->response = [];

        $data->response['message'] = $this->helpers->setJsonMessage('No content !');
        $data->status = Response::HTTP_NO_CONTENT;

        $requestData = (array) json_decode($request->getContent(), true);

        $video = new Videos();

        if (count($requestData) > 0) {
            if (filter_var($requestData['url'], FILTER_VALIDATE_URL)) {

                $video->setUrl($this->helpers->videoFormatURL($requestData['url']))
                    ->setTrick($trick)
                    ->setCreatedAt($this->helpers->now())
                ;

                $this->entityManager->persist($video);
                $this->entityManager->flush();

                $data->response['message'] = $this->helpers->setJsonMessage('Video created !', 'success');
                $data->response['video'] = $video->serialize();
                $data->status = Response::HTTP_CREATED;

                return $data;
            }

            $data->response['message'] = $this->helpers->setJsonMessage('Url not valid !');
            $data->status = Response::HTTP_NOT_ACCEPTABLE;
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function updateVideo(Request $request, Videos $video): object
    {
        $data = new stdClass;
        $data->response = [];

        $data->response['message'] = $this->helpers->setJsonMessage('No content !');
        $data->status = Response::HTTP_NO_CONTENT;

        $requestData = (array) json_decode($request->getContent(), true);

        if (count($requestData) > 0) {
            if (filter_var($requestData['url'], FILTER_VALIDATE_URL)) {

                $video->setUrl($this->helpers->videoFormatURL($requestData['url']));

                $this->entityManager->persist($video);
                $this->entityManager->flush();

                $data->response['message'] = $this->helpers->setJsonMessage('Video updated !', 'success');
                $data->response['video'] = $video->serialize();
                $data->status = Response::HTTP_OK;

                return $data;
            }

            $data->response['message'] = $this->helpers->setJsonMessage('Url not valid !');
            $data->status = Response::HTTP_NOT_ACCEPTABLE;
        }

        return $data;
    }

    /**
     * {@inheritDoc}
     */
    public function deleteImage($image): object
    {
        $data = new stdClass;
        $data->response = [
            'message' => $this->helpers->setJsonMessage('Image deleted', 'success'),
        ];
        $data->status = Response::HTTP_OK;

        if (is_file($image->getPath())) {
            unlink($image->getPath());
        }

        $this->entityManager->remove($image);
        $this->entityManager->flush();

        return $data;
    }

    /**
     * @param Videos $video
     *
     * @return object
     */
    public function deleteVideo(Videos $video): object
    {
        $data = new stdClass;
        $data->response = [
            'message' => $this->helpers->setJsonMessage('Video deleted'),
        ];
        $data->status = Response::HTTP_OK;

        $this->entityManager->remove($video);
        $this->entityManager->flush();

        return $data;
    }

    /**
     * @param Trick $trick
     *
     * @return object
     */
    public function deletePoster(Trick $trick): object
    {
        $data = new stdClass;
        $data->response = [
            'message' => $this->helpers->setJsonMessage('Poster deleted'),
        ];
        $data->status = Response::HTTP_OK;

        if (is_file($trick->getPoster())) {
            unlink($trick->getPoster());
        }

        $trick->setPoster();

        $this->entityManager->persist($trick);
        $this->entityManager->flush();

        return $data;
    }

    /**
     * @param Trick $trick
     * @param Request $request
     *
     * @return object
     */
    public function editTrick(Trick $trick, Request $request): object
    {
        $data = new stdClass;
        $data->response['message'] = $this->helpers->setJsonMessage('Trick updated', 'success');
        $data->status = Response::HTTP_OK;

        $requestData = json_decode($request->getContent());

        $trick->setUpdatedAt($this->helpers->now())
            ->setGroup($this->groupRepository->find((int) $requestData->group))
            ->setDescription($requestData->description)
        ;

        $this->entityManager->persist($trick);
        $this->entityManager->flush();

        return $data;
    }

    /**
     * @param Trick $trick
     *
     * @return void
     */
    public function deleteUploads(Trick $trick)
    {
        if (count($trick->getImage()) > 0) {
            foreach ($trick->getImage() as $image) {
                if ($image->getPath() !== "") {
                    unlink($image->getPath());
                }
            }
        }

        if ($trick->getPoster() !== "") {
            unlink($trick->getPoster());
        }
        return;
    }

    /**
     * @param int $page
     *
     * @return object
     */
    public function paginate(int $page = 0, int $items_per_page = 10, ?User $user): object
    {
        $tricks = $this->trickRepository->paginate($page, $items_per_page);

        $response = new stdClass;
        $response->tricks = $tricks;
        $response->last = false;
        $response->connected = $user !== null ? true : false;

        // Obtenir le nombre de page (tricks_total/items_per_page)
        $nb_page = (count($this->trickRepository->findAll()) / $items_per_page) - 1;
        // Verifier si c'est la dernière page
        $last_page = (int) ceil($nb_page);

        if ($last_page === $page) {
            $response->last = true;
        }

        return $response;
    }

}
