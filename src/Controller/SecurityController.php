<?php

namespace App\Controller;

use App\Form\ForgotPasswordType;
use App\Service\MailerService;
use App\Form\RegistrationType;
use App\Form\ResetPasswordType;
use App\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Form\FormError;

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
     * @Route("/reset-password")
     * @Template()
     */
    public function resetPassword(Request $request, UserPasswordEncoderInterface $encoder)
    {
        /** @var User $user */
        $user = $this->session->get('user');

        if (null === $user) {
            $this->addFlash('danger', 'Connexion reinitialisé, procédure de réinitialisation annulé. Veuillez effectuer une nouvelle demande.');

            return new RedirectResponse($this->generateUrl('app_index_index'));
        }


        $form = $this->createForm(ResetPasswordType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $encodedPassword = $encoder->encodePassword($user, $data['password']);
            $user->setPassword($encodedPassword);
            $this->em->merge($user);
            $this->em->flush();
            $this->session->remove('user');
            $this->addFlash('success', 'Votre mot de passe à été modifié.');

            return new RedirectResponse($this->generateUrl('app_index_index'));
        }

        return [
            'form' => $form->createView()
        ];
    }

    /**
     * @Route("/check/{passwordToken}")
     * @ParamConverter("user", class="App:User")
     * @return RedirectResponse
     */
    public function checkPasswordToken(User $user): RedirectResponse
    {
        $now = new \DateTime();
        if ($user->getPasswordRenewal() < $now) {
            $this->addFlash('danger', 'Le lien de réinitialisation a expiré. Effectuer une nouvelle demande si nécessaire.');

            return new RedirectResponse($this->generateUrl('app_index_index'));
        }

        $user->setPasswordRenewal(null);
        $user->setPasswordToken(null);
        $this->em->flush();

        $this->session->set('user', $user);
    
        return new RedirectResponse($this->generateUrl('app_security_resetpassword'));
    }

    /**
     * @Route("/forgot-password")
     * @Template()
     *
     * @param Request $request
     * @return mixed
     */
    public function forgotPassword(Request $request, MailerService $mailer)
    {
        $form = $this->createForm(ForgotPasswordType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            
            /** @var User $user */
            $user = $this->em->getRepository(User::class)->findOneBy(['email' => $data['email']]);
        
            if ($user !== null) {
                $user->generateToken(User::TOKEN_FOR_PASSWORD);
                $this->em->flush();
                $mailer->sendMailForForgotPassword($user);
                $this->addFlash('info', 'Vérifier votre boîte email.');
                
                return new RedirectResponse($this->generateUrl('app_index_index'));
            }

            $form->get('email')->addError(new FormError('Aucun compte avec cette adresse email'));
        }

        return [
            'form' => $form->createView()
        ];
    }

    /**
     * @Route("/register")
     * @template()
     */
    public function register(Request $request, UserPasswordEncoderInterface $encoder, MailerService $mailer)
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

            $mailer->sendRegisterMail($user);

            $this->em->persist($user);
            $this->em->flush();

            return $this->redirectToRoute('app_index_welcome');
        }

        return [
            'form' => $registrationForm->createView(),
            'title' => 'SnowTricks - Inscription'
        ];
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

        return new RedirectResponse($this->generateUrl('app_index_index'));
    }

    /**
     * @Route("/logout")
     */
    public function logout()
    {
        // No code here !
    }
}
