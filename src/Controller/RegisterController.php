<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\RegistrationType;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

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
    public function register(Request $request, UserPasswordEncoderInterface $encoder)
    {
        $registrationForm = $this->createForm(RegistrationType::class);

        $registrationForm->handleRequest($request);

        if ($registrationForm->isSubmitted() && $registrationForm->isValid()) {
            /** @var User $user */
            $user = $registrationForm->getData();
            $user->setCreatedAt(new \DateTime());

            $encodedPassword = $encoder->encodePassword($user, $user->getPassword());

            $user->setPassword($encodedPassword);
            
            $this->em->persist($user);
            $this->em->flush();

            $this->addFlash('success', 'Inscription enregistrée !');

            return $this->redirectToRoute('welcome');
        }

        return $this->render('register/register.html.twig', [
            'form' => $registrationForm->createView()
        ]);
    }

    /**
     * @Route("/welcome", name="welcome")
     */
    public function welcome()
    {
        return $this->render('register/welcome.html.twig');
    }
}
