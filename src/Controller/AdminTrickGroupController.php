<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\TrickGroupType;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\TrickGroup;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\RedirectResponse;

class AdminTrickGroupController extends AbstractController
{
    private $em;

    public function __construct(ObjectManager $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/admin/trick/group/", name="app_admin_trick_group")
     */
    public function index()
    {
        $trickGroups = $this->em->getRepository(TrickGroup::class)->findAll();

        return $this->render('backend/trickgroup/index.html.twig', [
            'groups' => $trickGroups
        ]);       
    }

    /**
     * @Route("/admin/trick/group/new", name="app_admin_trick_group_new")
     */
    public function new(Request $request)
    {
        $trickGroupForm = $this->createForm(TrickGroupType::class);

        $trickGroupForm->handleRequest($request);

        if ($trickGroupForm->isSubmitted() && $trickGroupForm->isValid()) {
            /** @var TrickGroup $trickGroup */
            $trickGroup = $trickGroupForm->getData();
            $trickGroup->setCreatedAt(new \DateTime());
            $this->em->persist($trickGroup);
            $this->em->flush();
            $this->addFlash('success', 'Trick group added');

            return new RedirectResponse($this->generateUrl('app_admin_trick_group'));
        }

        return $this->render('backend/trickgroup/new.html.twig', [
            'form' => $trickGroupForm->createView()
        ]);
    }

    /**
     * @Route("/admin/trick/group/{trickGroup}/", name="app_admin_trick_group_update",
     *  requirements={
     *      "trickGroup":"\d+"
     *  })
     */
    public function update(TrickGroup $trickGroup, Request $request)
    {
        $trickGroupForm = $this->createForm(TrickGroupType::class, $trickGroup, ['forAdd' => false]);

        $trickGroupForm->handleRequest($request);

        if ($trickGroupForm->isSubmitted() && $trickGroupForm->isValid()) {
            /** @var TrickGroup $trickGroup */
            $trickGroup = $trickGroupForm->getData();
            $this->em->merge($trickGroup);
            $this->em->flush();
            $this->addFlash('success', 'Trick group updated');

            return new RedirectResponse($this->generateUrl('app_admin_trick_group'));
        }

        return $this->render('backend/trickgroup/new.html.twig', [
            'form' => $trickGroupForm->createView()
        ]);
    }

    /**
     * @Route("/admin/trick/group/remove/{trickGroup}/", name="app_admin_trick_group_remove")
     */
    public function removeTrick(TrickGroup $trickGroup)
    {
        $this->em->remove($trickGroup);
        $this->em->flush();

        $this->addFlash('danger', 'trick group has been removed!');

        return new RedirectResponse($this->generateUrl('app_admin_trick_group'));
    }
}
