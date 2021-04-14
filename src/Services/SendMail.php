<?php 

namespace App\Services;

use App\Entity\User;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;

class SendMail {

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendTokenPassword(User $user, string $host) 
    {
        return $this->mailer->send((new TemplatedEmail())
            ->from('testwamp08@gmail.com')
            ->to( $user->getEmail() ) 
            ->subject('Modifier votre mot de passe')
            ->htmlTemplate('emails/forget-password.html.twig')
            ->context([
                'user' => $user,
                'website' => $host,
            ])
        );
    }

    public function sendTokenCorfirmation(User $user, string $host) 
    {
        return $this->mailer->send((new TemplatedEmail())
            ->from('testwamp08@gmail.com')
            ->to( $user->getEmail() ) 
            ->subject('Confirmation de votre adresse email')
            ->htmlTemplate('emails/confirm.html.twig')
            ->context([
                'user' => $user,
                'website' => $host,
            ])
        );
    }

}