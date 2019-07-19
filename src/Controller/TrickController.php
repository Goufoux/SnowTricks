<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\TrickType;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Trick;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\RedirectResponse;
use App\Entity\Media;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use App\Service\FileService;
use Symfony\Component\HttpFoundation\JsonResponse;

class TrickController extends AbstractController
{
    private $em;

    public function __construct(ObjectManager $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/trick/", name="app_trick")
     * @Template()
     */
    public function index()
    {
        $tricks = $this->em->getRepository(Trick::class)->findAll();

        return [
            'tricks' => $tricks
        ];
    }

    /**
     * @Route("/trick/update/{trick}", name="app_trick_update")
     * @Route("/trick/new", name="app_trick_new")
     * @Template()
     */
    public function new(Request $request, Trick $trick = null, FileService $fileService)
    {
        $trickForm = $this->createForm(TrickType::class, $trick);
        
        $trickForm->handleRequest($request);
        
        if ($trickForm->isSubmitted() && $trickForm->isValid()) {
            /** @var Trick $trick */
            $trick = $trickForm->getData();
            $trick->setCreatedAt(new \DateTime());
            $trick->setAuthor($this->getUser());

            /** @var array $files */
            $files = $request->files->get('trick')['media'] ?? [];
            
            $this->addFilesToTrick($files, $fileService, $trick);
            
            if (null === $trick->getId()) {
                $this->em->persist($trick);
                $this->addFlash('success', 'Trick créé !');
            } else {
                $this->em->merge($trick);
                $this->addFlash('info', 'Trick mis à jour !');
            }
            
            $this->em->flush();

            return new RedirectResponse($this->generateUrl('app_trick_new', ['trick' => $trick]));
        }

        return [
            'form' => $trickForm->createView(),
            'trick' => $trick
        ];
    }

    private function addFilesToTrick(array $files, FileService $fileService, Trick $trick)
    {
        foreach ($files as $file) {
            $fileService->uploadFile($file['file'], 'trick_directory');
            $media = new Media();
            $media->setMediaSrc($fileService->getFileName());
            $media->setTrick($trick);
            $trick->addMedium($media);
        }
    }

    /**
     * @Route("/trick/remove/{trick}", name="app_trick_remove")
     *
     * @param Trick $trick
     * @return RedirectResponse
     */
    public function remove(Trick $trick)
    {
        $this->em->remove($trick);
        $this->em->flush();

        $this->addFlash('danger', 'trick removed!');
        
        return new RedirectResponse($this->generateUrl('app_trick'));
    }

    /**
     * @Route("/trick/media/remove/{media}")
     *
     * @param Media $media
     */
    public function removeMedia(Media $media, FileService $fileService)
    {
        $fileService->deleteFile('trick_directory', $media->getMediaSrc());
        $this->em->remove($media);

        $this->em->flush();


        return new JsonResponse(true);
    }
}
