<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Service\FileService;

class UserController extends ObjectManagerController
{
    /**
     * @Route("/user/{user}")
     * @Template()
     * @param User $user
     */
    public function view(User $user)
    {
        return [
            'user' => $user,
            'title' => 'Profil - ' . $user->getUsername()
        ];
    }

    /**
     * @Route("/user/avatar/remove/{user}")
     */
    public function removeAvatar(User $user, FileService $fileService)
    {
        $fileService->deleteFile('avatar_directory', $user->getAvatar());
        $user->setAvatar(null);
        
        $this->em->flush();
     
        return new JsonResponse('ok');
    }
}
