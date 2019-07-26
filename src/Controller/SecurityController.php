<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Form\RegistrationType;
use App\Entity\User;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class SecurityController extends ObjectManagerController
{
    /**
     * @Route("/login")
     * @Template()
     */
    public function login(AuthenticationUtils $authenticationUtils): array
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return ['last_username' => $lastUsername, 'error' => $error, 'title' => 'SnowTricks - Connexion'];
    }

    /**
     * @Route("/register")
     * @template()
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
            $user->generateToken(User::TOKEN_FOR_REGISTRATION);
            $user->setActive(false);

            $this->sendRegisterMail($user, $mailer);

            $this->em->persist($user);
            $this->em->flush();

            return $this->redirectToRoute('welcome');
        }

        return [
            'form' => $registrationForm->createView(),
            'title' => 'SnowTricks - Inscription'
        ];
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
     * @Route("/register/{registerToken}")
     * @ParamConverter("user", class="App:User")
     */
    public function confirmRegistration(User $user)
    {
        $user->setActive(true);
        $user->setRegisterToken(null);
        
        $this->em->flush();

        $this->addFlash('success', 'Compte activé. Vous pouvez désormais vous connecter.');

        return new RedirectResponse($this->generateUrl('app_index'));

    }


    /**
     * @Route("/logout")
     */
    public function logout()
    {
        
    }
}
