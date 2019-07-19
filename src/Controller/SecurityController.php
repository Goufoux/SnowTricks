<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Form\RegistrationType;
use App\Entity\User;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Doctrine\Common\Persistence\ObjectManager;

class SecurityController extends AbstractController
{
    private $em;

    public function __construct(ObjectManager $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('frontend/security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error, 'title' => 'SnowTricks - Connexion']);
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
            $user->setActive(false);

            $this->sendRegisterMail($user, $mailer);

            $this->em->persist($user);
            $this->em->flush();

            return $this->redirectToRoute('welcome');
        }

        return $this->render('frontend/register/register.html.twig', [
            'form' => $registrationForm->createView(),
            'title' => 'SnowTricks - Inscription'
        ]);
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
        return $this->render('frontend/register/welcome.html.twig');
    }

    /**
     * @Route("/register/{token}", name="app_register_confirm")
     * @ParamConverter("user", class="Entity:User")
     *
     * @param [string] $token
     */
    public function confirmRegistration(User $user)
    {
        $user->setActive(true);
        $user->setRegisterToken(null);
        
        $this->em->merge($user);
        $this->em->flush();

        $this->addFlash('success', 'Compte activé. Vous pouvez désormais vous connecter.');

        return new RedirectResponse($this->generateUrl('app_index'));

    }


    /**
     * @Route("/logout", name="app_logout", methods={"GET"})
     */
    public function logout()
    {
        
    }
}
