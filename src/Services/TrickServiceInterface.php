<?php

namespace App\Services;

use App\Entity\Image;
use App\Entity\Trick;
use App\Entity\User;
use App\Entity\Videos;
use Symfony\Component\HttpFoundation\Request;

interface TrickServiceInterface
{

    /**
     * @param  Request $request
     * @return object
     */
    public function store(Request $request): object;

    /**
     * @param Request $request
     * @param Trick $trick
     *
     * @return object
     */
    public function updatePoster(Request $request, Trick $trick): object;

    /**
     * @param Request $request
     * @param Image $image
     *
     * @return object
     */
    public function updateImage(Request $request, Image $image): object;

    /**
     * @param Request $request
     * @param Trick $trick
     *
     * @return object
     */
    public function addImage(Request $request, Trick $trick): object;

    /**
     * @param Request $request
     * @param Trick $trick
     *
     * @return object
     */
    public function addVideo(Request $request, Trick $trick): object;

    /**
     * @param Request $request
     * @param Videos $videos
     *
     * @return object
     */
    public function updateVideo(Request $request, Videos $videos): object;

    /**
     * @param Image $image
     *
     * @return object
     */
    public function deleteImage(Image $image): object;

    /**
     * @param Videos $video
     *
     * @return object
     */
    public function deleteVideo(Videos $video): object;

    /**
     * @param Trick $trick
     *
     * @return object
     */
    public function deletePoster(Trick $trick): object;

    /**
     * @param Trick $trick
     * @param Request $request
     *
     * @return object
     */
    public function editTrick(Trick $trick, Request $request): object;

    /**
     * @param Trick $trick
     *
     * @return void
     */
    public function deleteUploads(Trick $trick);

    /**
     * @param int $page
     * @return object
     */
    public function paginate(int $page = 0, int $items_per_page = 10, ?User $user): object;

}
