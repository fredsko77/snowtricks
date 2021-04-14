<?php

namespace App\Controller;

use App\Entity\Group;
use App\Form\GroupType;
use App\Helpers\Helpers;
use App\Repository\GroupRepository;
use App\Repository\TrickRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/group")
 */
class GroupController extends AbstractController
{

    public function __construct(GroupRepository $groupRepository, Helpers $helpers, TrickRepository $trickRepository)
    {
        $this->helpers = $helpers;
        $this->groupRepository = $groupRepository;
        $this->trickRepository = $trickRepository;
    }

    /**
     * @Route("/list", name="group_index", methods={"GET"})
     */
    public function index(): Response
    {
        return $this->render('group/index.html.twig', [
            'groups' => $this->groupRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="group_new", methods={"GET","POST"})
     */
    function new (Request $request): Response {
        $group = new Group();
        $form = $this->createForm(GroupType::class, $group);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $group->setSlug($this->helpers->generateSlug($group->getName()));
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($group);
            $entityManager->flush();

            return $this->redirectToRoute('group_index');
        }

        return $this->render('group/new.html.twig', [
            'group' => $group,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="group_show", methods={"GET"})
     */
    public function show(Group $group): Response
    {
        return $this->render('group/show.html.twig', [
            'group' => $group,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="group_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Group $group): Response
    {
        $form = $this->createForm(GroupType::class, $group);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('group_index');
        }

        return $this->render('group/edit.html.twig', [
            'group' => $group,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="group_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Group $group): Response
    {
        if ($this->isCsrfTokenValid('delete' . $group->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($group);
            $entityManager->flush();
        }

        return $this->redirectToRoute('group_index');
    }

    /**
     * @Route(
     *  "/{slug}/{id}/tricks",
     *  name="group_tricks",
     *  requirements={"id":"\d+", "slug"="[\/A-Za-z0-9\-]+"},
     *  methods={"GET"}
     * )
     */
    public function tricks(Group $group, string $slug): Response
    {
        if ($slug !== $group->getSlug()) {
            return $this->redirectToRoute('group_tricks', [
                'id' => $group->getId(),
                'slug' => $group->getSlug(),
            ], Response::HTTP_MOVED_PERMANENTLY);
        }

        return $this->render('group/tricks.html.twig', [
            'tricks' => $this->trickRepository->group($group),
            'group' => $group,
        ]);
    }
}
