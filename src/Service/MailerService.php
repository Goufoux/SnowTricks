<?php

namespace App\Service;

use App\Entity\User;

class MailerService
{
    private $mailer;
    private $templating;

    public function __construct(\Swift_Mailer $swift_Mailer, \Twig\Environment $templating)
    {
        $this->mailer = $swift_Mailer;
        $this->templating = $templating;
    }

    public function sendMailForForgotPassword(User $user)
    {
        $message = (new \Swift_Message('Réinitialisation du mot de passe'))
            ->setFrom('admin@genarkys.fr')
            ->setTo($user->getEmail())
            ->setBody(
                $this->templating->render(
                    'mail/forgot-password.html.twig',
                    [
                        'username' => $user->getUsername(),
                        'token' => $user->getPasswordToken()
                    ]
                ),
                'text/html'
        );
        $this->mailer->send($message);
    }

    public function sendRegisterMail(User $user)
    {
        $message = (new \Swift_Message('Inscription sur SnowTricks !'))
                        ->setFrom('admin@genarkys.fr')
                        ->setTo($user->getEmail())
                        ->setBody(
                            $this->templating->render(
                                'mail/registration.html.twig',
                                [
                                    'username' => $user->getUsername(),
                                    'token' => $user->getRegisterToken()
                                ]
                            ),
                            'text/html'
                        );
            
        $this->mailer->send($message);
    }
}