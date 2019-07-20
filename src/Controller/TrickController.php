<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use App\Form\TrickType;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Trick;
use Symfony\Component\HttpFoundation\RedirectResponse;
use App\Entity\Media;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use App\Service\FileService;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\VideoLink;

class TrickController extends ObjectManagerController
{
    /**
     * @Route("/trick/")
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

            $videoLinks = $request->request->get('trick')['videoLinks'] ?? [];
            
            $this->addFilesToTrick($files, $fileService, $trick);

            $this->addVideoLinksToTrick($videoLinks, $trick);
            
            if (null === $trick->getId()) {
                $this->em->persist($trick);
                $this->addFlash('success', 'Trick créé !');
            } else {
                $this->addFlash('info', 'Trick mis à jour !');
            }
            
            $this->em->flush();

            return new RedirectResponse($this->generateUrl('app_trick_update', ['trick' => $trick->getId()]));
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

    private function addVideoLinksToTrick(array $videoLinks, Trick $trick)
    {
        foreach ($videoLinks as $source) {
            $videoLink = new VideoLink();
            $videoLink->setSource($source['source']);
            $videoLink->setCreatedAt(new \DateTime());
            $videoLink->setTrick($trick);
            $trick->addVideoLink($videoLink); 
        }
    }

    /**
     * @Route("/trick/remove/{trick}")
     *
     * @param Trick $trick
     * @return RedirectResponse
     */
    public function remove(Trick $trick)
    {
        $this->em->remove($trick);
        $this->em->flush();

        $this->addFlash('danger', 'Trick supprimé !');
        
        return new RedirectResponse($this->generateUrl('app_trick_index'));
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
