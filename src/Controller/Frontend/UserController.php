<?php

namespace App\Controller\Frontend;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Avatar;

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

    /**
     * @Route("/user/avatar/remove/{user}", name="app_user_avatar_remove")
     */
    public function removeAvatar(User $user)
    {
        $user->setAvatar(null);
        // $user->removeAvatar();
        $this->em->merge($user);
        
        $this->em->flush();
     
        return new JsonResponse('ok');
    }
}
