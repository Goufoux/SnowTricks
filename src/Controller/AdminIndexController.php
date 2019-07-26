<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Entity\Trick;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use App\Entity\Comment;

class AdminIndexController extends ObjectManagerController
{
    /**
     * @Route("/admin")
     * @Template()
     */
    public function index()
    {
        $lastRegisteredUsers = $this->em->getRepository(User::class)->findBy([], ['createdAt' => 'DESC'], 5); 

        $lastTricksAdded = $this->em->getRepository(Trick::class)->findBy([], ['createdAt' => 'DESC'], 5);

        $lastCommentsAdded = $this->em->getRepository(Comment::class)->findBy([], ['createdAt' => 'DESC'], 5);

        return [
            'title' => 'SnowTricks - Backend',
            'users' => $lastRegisteredUsers,
            'tricks' => $lastTricksAdded,
            'comments' => $lastCommentsAdded
        ];
    }
}
