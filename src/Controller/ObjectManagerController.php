<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class ObjectManagerController extends AbstractController
{
    public $em;
    public $session;

    public function __construct(ObjectManager $em, SessionInterface $sessionInterface)
    {
        $this->em = $em;
        $this->session = $sessionInterface;   
    }
}
