<?php
namespace App\Services;

use App\Entity\Comments;
use App\Entity\Trick;
use App\Entity\User;
use App\Helpers\Helpers;
use App\Repository\CommentsRepository;
use App\Services\CommentsServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use stdClass;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CommentsService implements CommentsServiceInterface
{
    /**
     * @var EntityManagerInterface $entityManager
     */
    private $entityManager;

    /**
     * @var Helpers $helpers
     */
    private $helpers;

    public function __construct(EntityManagerInterface $entityManager, CommentsRepository $commentsRepository, Helpers $helpers)
    {
        $this->entityManager = $entityManager;
        $this->commentsRepository = $commentsRepository;
        $this->helpers = $helpers;
    }

    /**
     * {@inheritDoc}
     */
    public function store(Request $request, Trick $trick, User $user): object
    {
        $data = json_decode($request->getContent());
        $response = new stdClass;
        $response->response['message'] = $this->helpers->setJsonMessage('No content');
        $response->status = Response::HTTP_NO_CONTENT;
        $response->response['connected']['id'] = $user !== null ? $user->getId() : null;

        if (property_exists($data, 'comment') && $data->comment !== '') {

            $comment = new Comments();
            $comment
                ->setTrick($trick)
                ->setUser($user)
                ->setCreatedAt($this->helpers->now())
                ->setComment($data->comment)
            ;

            // $this->entityManager->persist($comment);
            // $this->entityManager->flush();

            $response->response['comment'] = $comment->serialize();
            $response->response['message'] = $this->helpers->setJsonMessage('Comment created 🚀', 'success');
            $response->status = Response::HTTP_CREATED;
        }

        return $response;
    }

    /**
     * {@inheritDoc}
     */
    public function delete(Comments $comment): object
    {
        $data = new stdClass;
        $data->status = Response::HTTP_OK;
        $data->response['message'] = $this->helpers->setJsonMessage('Comment deleted !', 'info');
        $data->response['comment'] = $comment->getId();

        $this->entityManager->remove($comment);
        $this->entityManager->flush();

        return $data;
    }

    /**
     * {@inheritDoc}
     */
    public function paginate(Trick $trick, ?User $user, int $page = 0, int $items_per_page = 10): object
    {
        $response = new stdClass;
        $response->last = false;
        $comments = $this->commentsRepository->paginate($trick, $page, $items_per_page);
        $response->connected['id'] = $user !== null ? $user->getId() : null;
        $json_comments = [];

        foreach ($comments as $comment) {
            $json_comments[] = $comment->serialize();
        }

        $response->comments = $json_comments;

        // Obtenir le nombre de page (tricks_total/items_per_page)
        $nb_page = (count($this->commentsRepository->findAll()) / $items_per_page) - 1;
        // Verifier si c'est la dernière page
        $last_page = (int) ceil($nb_page) - 1;

        if ($last_page === $page || ($last_page !== $page && count($response->comments) < $items_per_page)) {
            $response->last = true;
        }

        return $response;
    }
}
