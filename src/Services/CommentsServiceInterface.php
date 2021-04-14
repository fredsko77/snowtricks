<?php
namespace App\Services;

use App\Entity\Comments;
use App\Entity\Trick;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;

interface CommentsServiceInterface
{

    /**
     * @param Request $request
     * @param Trick $trick
     * @param User $user
     *
     * @return object
     */
    public function store(Request $request, Trick $trick, User $user): object;

    /**
     * @param Comments $comments
     * @return object
     */
    public function delete(Comments $comment): object;

    /**
     * @param int $page
     * @param int $items_per_page
     *
     * @return object
     */
    public function paginate(Trick $trick, ?User $user, int $page = 0, int $items_per_page = 5): object;

}
