<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class IndexController extends ObjectManagerController
{
    /**
     * @Route("/")
     * @Template()
     *
     * @return void
     */
    public function index()
    {
        return [];
    }

    /**
     * @Route("/welcome")
     * @Template()
     */
    public function welcome()
    {
        return [];
    }
}
