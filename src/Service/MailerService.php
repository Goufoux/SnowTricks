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

    public function sendMail(User $user, string $template, array $data)
    {
        $message = (new \Swift_Message('Réinitialisation du mot de passe'))
            ->setFrom('admin@genarkys.fr')
            ->setTo($user->getEmail())
            ->setBody(
                $this->templating->render(
                    $template,
                    $data
                ),
                'text/html'
        );
        return $this->mailer->send($message);
    }

    public function sendMailForForgotPassword(User $user)
    {
        $data = [
            'username' => $user->getUsername(),
            'token' => $user->getPasswordToken()
        ];

        return $this->sendMail($user, 'mail/forgot-password.html.twig', $data);
    }

    public function sendRegisterMail(User $user)
    {
        $data = [
            'username' => $user->getUsername(),
            'token' => $user->getRegisterToken()
        ];

        return $this->sendMail($user, 'mail/registration.html.twig', $data);
    }
}