<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use App\Form\TrickGroupType;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\TrickGroup;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\RedirectResponse;

class AdminTrickGroupController extends ObjectManagerController
{
    /**
     * @Route("/admin/trick/group/")
     * @Template()
     */
    public function index()
    {
        $trickGroups = $this->em->getRepository(TrickGroup::class)->findAll();

        return [
            'groups' => $trickGroups
        ];       
    }

    /**
     * @Route("/admin/trick/group/new")
     * @Route("/admin/trick/group/{trickGroup}/", name="app_admintrickgroup_update")
     * @Template()
     */
    public function new(Request $request, TrickGroup $trickGroup = null)
    {
        $trickGroupForm = $this->createForm(TrickGroupType::class, $trickGroup);

        $trickGroupForm->handleRequest($request);

        if ($trickGroupForm->isSubmitted() && $trickGroupForm->isValid()) {
            /** @var TrickGroup $trickGroup */
            $trickGroup = $trickGroupForm->getData();
            
            if (null === $trickGroup->getId()) {
                $trickGroup->setCreatedAt(new \DateTime());
                $this->em->persist($trickGroup);
                $this->addFlash('success', 'Groupe de trick ajouté !');
            } else {
                $this->addFlash('success', 'Groupe de trick mise à jour !');
            }

            $this->em->flush();

            return new RedirectResponse($this->generateUrl('app_admintrickgroup_index'));
        }

        return [
            'form' => $trickGroupForm->createView(),
            'trickGroup' => $trickGroup
        ];
    }

    /**
     * @Route("/admin/trick/group/remove/{trickGroup}/")
     */
    public function remove(TrickGroup $trickGroup)
    {
        $this->em->remove($trickGroup);
        $this->em->flush();

        $this->addFlash('danger', 'Groupe de trick supprimé !');

        return new RedirectResponse($this->generateUrl('app_admintrickgroup_index'));
    }
}
