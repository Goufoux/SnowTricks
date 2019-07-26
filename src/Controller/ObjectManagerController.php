<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\Common\Persistence\ObjectManager;

class ObjectManagerController extends AbstractController
{
    public $em;

    public function __construct(ObjectManager $em)
    {
        $this->em = $em;   
    }
}
