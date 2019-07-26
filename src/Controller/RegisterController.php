<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\RegistrationType;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

class RegisterController extends AbstractController
{
    private $em;

    public function __construct(ObjectManager $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/register", name="register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $encoder, \Swift_Mailer $mailer)
    {
        $registrationForm = $this->createForm(RegistrationType::class);

        $registrationForm->handleRequest($request);

        if ($registrationForm->isSubmitted() && $registrationForm->isValid()) {
            /** @var User $user */
            $user = $registrationForm->getData();
            $user->setCreatedAt(new \DateTime());

            $encodedPassword = $encoder->encodePassword($user, $user->getPassword());

            $user->setPassword($encodedPassword);
            $registerToken = uniqid();
            $user->setRegisterToken($registerToken);

            $this->sendRegisterMail($user, $mailer);

            $this->em->persist($user);
            $this->em->flush();

            return $this->redirectToRoute('welcome');
        }

        return $this->render('register/register.html.twig', [
            'form' => $registrationForm->createView()
        ]);
    }

    /**
     * @Route("/register/{token}", name="app_register_confirm")
     *
     * @param [string] $token
     */
    public function confirmRegistration($token)
    {
        /** @var User $user */
        $user = $this->em->getRepository(User::class)->findOneBy(['register_token' => $token]);

        if ($user === null) {
            $this->addFlash('danger', 'Token invalid.');
            return new RedirectResponse($this->generateUrl('app_index'));
        }

        $user->setActive(true);
        $user->setRegisterToken(null);
        
        $this->em->merge($user);
        $this->em->flush();

        $this->addFlash('success', 'Compte activé. Vous pouvez désormais vous connecter.');

        return new RedirectResponse($this->generateUrl('app_index'));

    }

    private function sendRegisterMail(User $user, \Swift_Mailer $mailer)
    {
        $message = (new \Swift_Message('Inscription sur SnowTricks !'))
                        ->setFrom('admin@genarkys.fr')
                        ->setTo($user->getEmail())
                        ->setBody(
                            $this->renderView(
                                'mail/registration.html.twig',
                                [
                                    'username' => $user->getUsername(),
                                    'token' => $user->getRegisterToken()
                                ]
                            ),
                            'text/html'
                        );
            
        $state = $mailer->send($message);
    }

    /**
     * @Route("/welcome", name="welcome")
     */
    public function welcome()
    {
        return $this->render('register/welcome.html.twig');
    }
}
