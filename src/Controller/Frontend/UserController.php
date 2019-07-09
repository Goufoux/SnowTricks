<?php

namespace App\Controller\Frontend;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\User;

class UserController extends AbstractController
{
    private $em;

    public function __construct(ObjectManager $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/user/{user}", name="app_user_view")
     * @param User $user
     */
    public function view(User $user)
    {
        return $this->render('frontend/user/view.html.twig', [
            'user' => $user,
            'title' => 'Profil - ' . $user->getUsername()
        ]);
    }
}
