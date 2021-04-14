<?php

namespace App\Services;

use App\Entity\User;
use App\Helpers\Helpers;
use App\Repository\UserRepository;
use App\Services\SendMail;
use App\Services\UserServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class UserService implements UserServiceInterface
{

    /**
     * @var UrlGeneratorInterface $urlGenerator
     */
    private $urlGenerator;

    /**
     * @var UsersRepository $repository
     */
    private $repository;

    /**
     * @var EntityManagerInterface $entityManager
     */
    private $entityManager;

    /**
     * @var SendMail $sendMail
     */
    private $sendMail;

    private $helpers;

    public function __construct(UserRepository $repository, EntityManagerInterface $entityManager, UrlGeneratorInterface $urlGenerator, SendMail $sendMail)
    {
        $this->helpers = new Helpers;
        $this->urlGenerator = $urlGenerator;
        $this->entityManager = $entityManager;
        $this->repository = $repository;
        $this->sendMail = $sendMail;
    }

    /**
     * {@inheritdoc}
     */
    public function store(Request $request): array
    {
        $data = json_decode($request->getContent(), true);
        $user = new User();
        $type = 'danger';
        $host = "{$request->getScheme()}://{$request->getHost()}";
        $token = $this->helpers->generateToken(80);
        if ($this->helpers->isFilled($data)) {
            if ($this->repository->findOneBy(['email' => $data['email']]) === null) {
                if ($this->helpers->passValid($data['password'])) {
                    $user->_hydrate($data)
                        ->setToken($token)
                        ->setCreatedAt($this->helpers->now())
                    ;

                    $this->sendMail->sendTokenCorfirmation($user, $host);

                    $this->entityManager->persist($user);
                    $this->entityManager->flush();

                    return [
                        'data' => [
                            'message' => $this->helpers->setJsonMessage('Un email de confirmation vous a été envoyé ✅', 'success'),
                            'url' => $this->urlGenerator->generate('home'),
                        ],
                        'status' => Response::HTTP_OK,
                    ];
                }

                return [
                    'data' => [
                        'message' => $this->helpers->setJsonMessage('Votre mot de passe n\'est pas valide !', 'warning'),
                        'url' => $this->urlGenerator->generate('home'),
                    ],
                    'status' => Response::HTTP_BAD_REQUEST,
                ];
            }

            return [
                'data' => [
                    'message' => $this->helpers->setJsonMessage('Cette adresse email est déja utilisé 🤕 !', 'warning'),
                ],
                'status' => Response::HTTP_BAD_REQUEST,
            ];
        }

        $content = 'Tous les champs doivent être remplis';
        $code = Response::HTTP_BAD_REQUEST;

        return [
            'data' => $this->helpers->setJsonMessage($content, $type),
            'status' => $code,
        ];

    }

}
