<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\User;
use App\Entity\Trick;
use App\Entity\Comment;

class AdminIndexController extends AbstractController
{
    private $em;

    public function __construct(ObjectManager $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/admin", name="app_admin_index")
     */
    public function index()
    {
        $lastRegisteredUsers = $this->em->getRepository(User::class)->findBy([], ['createdAt' => 'DESC'], 5); 

        $lastTricksAdded = $this->em->getRepository(Trick::class)->findBy([], ['createdAt' => 'DESC'], 5);

        $lastCommentsAdded = $this->em->getRepository(Comment::class)->findBy([], ['createdAt' => 'DESC'], 5);

        return $this->render('backend/index.html.twig', [
            'title' => 'SnowTricks - Backend',
            'users' => $lastRegisteredUsers,
            'tricks' => $lastTricksAdded,
            'comments' => $lastCommentsAdded
        ]);
    }
}
