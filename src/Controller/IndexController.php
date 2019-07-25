<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use App\Entity\Trick;

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
        $tricks = $this->em->getRepository(Trick::class)->findBy([], ['createdAt' => 'DESC'], 15, 0);

        return [
            'tricks' => $tricks
        ];
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
