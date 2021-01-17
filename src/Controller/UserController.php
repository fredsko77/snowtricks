<?php

namespace App\Controller;

use App\Entity\User;
use App\Helpers\Helpers;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{
    /**
     * @var UsersRepository $repository
     */
    private $repository;

    /**
     * @var  EntityManagerInterface $entityManager
     */
    private $entityManager;

    /**
     * @var Helpers $helpers
     */
    private $helpers;

    public function __construct(UserRepository $repository, EntityManagerInterface $entityManager)
    {
        $this->helpers = new Helpers;
        $this->repository = $repository;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/user", name="user")
     */
    public function index(): Response
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    /**
     * @Route(
     *      "/register", 
     *      name="register", 
     *      methods={"GET"}
     * )
     */
    public function register(): Response
    {
        return $this->render('auth/register.html.twig', [
            'helpers' => $this->helpers,
            'title' => 'Inscription ',
        ]);
    }

    /**
     * @Route(
     *      "/api_register",
     *      name="api_register",
     *      methods={"POST"}
     * )
     */
    public function store(Request $request, UserPasswordEncoderInterface $encoder, MailerInterface $mailer) :JsonResponse
    {
        $response = new JsonResponse();
        $response->headers->set('Content-Type', 'application/json');
        $data = (object) json_decode($request->getContent(), true);
        $user = new User();
        $token = $this->helpers->generateToken(80);
        if ( $this->helpers->isFilled($data) ) {
            if ( $this->repository->findOneBy(['email' => $data->email]) === null ) {
                if ( $this->helpers->passValid($data->password) ) {
                    $user   ->setEmail( $data->email )
                            ->setPseudo( $data->pseudo )
                            ->setPassword( $encoder->encodePassword($user, $data->password ) )
                            ->setToken( $token )
                            ->setCreatedAt( $this->helpers->now() )
                            ;
                    $email = (new TemplatedEmail())
                            ->from('testwamp08@gmail.com')
                            ->to( $data->email ) 
                            ->subject('Confirmation de votre adresse email')
                            ->htmlTemplate('emails/confirm.html.twig')
                            ->context([
                                'user' => $user,
                                'token' => $token,
                                'website' => $request->getHost(),
                            ])
                            ;
                    $mailer->send($email);
                    $this->entityManager->persist($user);
                    $this->entityManager->flush();
                    $response->setData([ 
                        'message' => $this->helpers->setJsonMessage('Un email de cnfirmation vous a été envoyé ✅', 'success'),
                        'url' => $this->generateUrl('home'),
                    ]);
                    $response->setStatusCode(Response::HTTP_OK);
                    return $response;
                }
                
                $response->setData([
                    'message' => $this->helpers->setJsonMessage('Votre mot de passe n\'est pas valide !', 'warning'),
                ]);
                $response->setStatusCode(Response::HTTP_BAD_REQUEST);
                return $response;
            }

            $response->setData([
                'message' => $this->helpers->setJsonMessage('Cette adresse email est déja utilisé 🤕 '),
            ]);
            $response->setStatusCode(Response::HTTP_BAD_REQUEST);
            return $response;
        }
        
        $response->setData([
            'message' => $this->helpers->setJsonMessage('Tous les champs doivent être remplis'),
        ]);
        $response->setStatusCode(Response::HTTP_BAD_REQUEST);
        return $response;

    }
    
    /**
     * @Route(
     *      "/users/confirm/{token}", 
     *      name="confirm_user", 
     *      requirements={"token"="[a-zA-Z0-9]+"},
     *      methods={"GET"}
     * )
     */
    public function confirm(string $token): Response
    {
        $exist = $this->repository->findOneBy(['token' => $token]);
        $errors = null;
        if ( $exist instanceof User ) {
            $exist-> setConfirm(true);
            $exist->setToken($this->helpers->generateToken(80));
            $this->entityManager->persist($exist);
            $this->entityManager->flush();
        } else {
            $errors = true;
        }
        return $this->render('auth/confirm.html.twig', [
            'errors' => $errors,
        ]);
    }

    /**
     * @Route(
     *      "/auth/forget-password",
     *      name="auth_forget_password",
     *      methods={"GET"}
     * )
     */
    public function forgetPassword() :Response
    {
        // if ($this->getUser() instanceof User) {
        //     return $this->redirectToRoute('home');
        // }
        return $this->render("auth/forget-password.html.twig", []);
    }

    /**
     * @Route(
     *      "/api/user/change-password/{token}",
     *      name="api_user_change_password",
     *      requirements={"token"="[a-zA-Z0-9]+"},
     *      methods={"GET"}
     * )
     */
    public function changePassword(string $token, Request $request) :JsonResponse 
    {
        $response = new JsonResponse;
        $response->headers->set('Content-Type', 'application/json');
        $data = (object) json_decode($request->getContent(), true);
        return $response;
    }

    /**
     * @Route(
     *      "/api/user/send_token_password",
     *      name="api_user_send_token_password",
     *      requirements={"token"="[a-zA-Z0-9]+"},
     *      methods={"POST"}
     * )
     */
    public function sendTokenPassword(Request $request, string $token) :JsonResponse
    {
        $response = new JsonResponse;
        $response->headers->set('Content-Type', 'application/json');
        $data = (object) json_decode($request->getContent(), true);
        if ($data->email !== "") {

        }
        $response->setStatusCode(Response::HTTP_NO_CONTENT).
        $response->setData([]);
        return $response;
    }

}
