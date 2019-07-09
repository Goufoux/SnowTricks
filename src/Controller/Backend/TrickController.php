<?php

namespace App\Controller\Backend;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\TrickType;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Trick;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\RedirectResponse;

class TrickController extends AbstractController
{
    private $em;

    public function __construct(ObjectManager $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/admin/trick/", name="app_admin_trick")
     */
    public function index()
    {
        $tricks = $this->em->getRepository(Trick::class)->findAll();

        return $this->render('backend/trick/index.html.twig', [
            'tricks' => $tricks
        ]);
    }

    /**
     * @Route("/admin/trick/new", name="app_admin_trick_new")
     */
    public function new(Request $request)
    {
        $trickForm = $this->createForm(TrickType::class);

        $trickForm->handleRequest($request);

        if ($trickForm->isSubmitted() && $trickForm->isValid()) {
            /** @var Trick $trick */
            $trick = $trickForm->getData();
            $trick->setCreatedAt(new \DateTime());
            $trick->setAuthor($this->getUser());
            
            $this->em->persist($trick);
            $this->em->flush();

            $this->addFlash('success', 'Trick added !');

            return new RedirectResponse($this->generateUrl('app_admin_trick'));
        }

        return $this->render('backend/trick/new.html.twig', [
            'form' => $trickForm->createView()
        ]);
    }

    /**
     * @Route("/admin/trick/update/{trick}", name="app_admin_trick_update")
     *
     * @param Trick $trick
     * @param Request $request
     */
    public function update(Trick $trick, Request $request)
    {
        $trickForm = $this->createForm(TrickType::class, $trick, ['forAdd' => false]);

        $trickForm->handleRequest($request);

        $mediaGroup = $trick->getMedia();

        if ($trickForm->isSubmitted() && $trickForm->isValid()) {
            /** @var Trick $trick */
            $trick = $trickForm->getData();
            $trick->setUpdatedAt(new \DateTime());
            $trick->setAuthor($this->getUser());
            
            $this->em->merge($trick);
            $this->em->flush();

            $this->addFlash('success', 'Trick updated !');

            return new RedirectResponse($this->generateUrl('app_admin_trick'));
        }

        

        return $this->render('backend/trick/new.html.twig', [
            'form' => $trickForm->createView()
        ]);
    }

    /**
     * @Route("/admin/trick/remove/{trick}", name="app_admin_trick_remove")
     *
     * @param Trick $trick
     * @return RedirectResponse
     */
    public function remove(Trick $trick)
    {
        $this->em->remove($trick);
        $this->em->flush();

        $this->addFlash('danger', 'trick removed!');
        
        return new RedirectResponse($this->generateUrl('app_admin_trick'));        
    }
}
