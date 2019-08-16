<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use App\Form\TrickType;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Trick;
use Symfony\Component\HttpFoundation\RedirectResponse;
use App\Entity\Media;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use App\Service\FileService;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\VideoLink;
use App\Form\CommentType;
use App\Entity\Comment;
use App\Service\Helper;
use Symfony\Component\Form\FormError;

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
     * @Route("/trick/view/{slug}")
     * @ParamConverter("trick", class="App:Trick")
     * @Template()
     *
     * @param Trick $trick
     */
    public function view(Trick $trick = null, Request $request)
    {
        $comments = $this->em->getRepository(Comment::class)->findBy(['trick' => $trick], ['createdAt' => 'DESC'], 5);
        $form = $this->createForm(CommentType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment = $form->getData();
            $comment->setCreatedAt(new \DateTime());
            $comment->setTrick($trick);
            $comment->setAuthor($this->getUser());

            $this->em->persist($comment);
            $this->em->flush();

            $this->addFlash('success', 'Votre commentaire a été ajouté !');

            return new RedirectResponse($this->generateUrl('app_trick_view', ['slug' => $trick->getSlug()]));
        }

        return [
            'trick' => $trick,
            'comments' => $comments,
            'form' => $form->createView()
        ];
    }

    /**
     * @Route("/trick/{id}/comments/{limit}/{offset}")
     * @Template()
     */
    public function getComments(Trick $trick, $limit, $offset)
    {
        $comments = $this->em->getRepository(Comment::class)->findBy(['trick' => $trick], ['createdAt' => 'DESC'], $limit, $offset);
        
        if (empty($comments)) {
            return new JsonResponse(false);
        }

        return [
            'comments' => $comments
        ];
    }

    /**
     * @Route("/trick/load/{limit}/{offset}")
     * @Template
     *
     * @param [int] $limit
     * @param [int] $offset
     * @return array
     */
    public function getTricks($limit, $offset)
    {
        $tricks = $this->em->getRepository(Trick::class)->findBy([], ['createdAt' => 'DESC'], $limit, $offset);

        if (empty($tricks)) {
            return new JsonResponse(false);
        }

        return [
            'tricks' => $tricks
        ];
    }

    /**
     * @Route("/trick/update/{slug}", name="app_trick_update")
     * @ParamConverter("trick", class="App:Trick")
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
            $trick->setAuthor($this->getUser());
            
            /** @var array $files */
            $files = $request->files->get('trick')['media'] ?? [];
            
            $slug = Helper::slugify($trick->getName());
            
            if (false === $slug) {
                $trickForm->get('name')->addError(new FormError('Le nom ne semble pas valide'));

                goto out;
            }

            $trick->setSlug($slug);
            $videoLinks = $request->request->get('trick')['videoLinks'] ?? [];
            
            $this->addFilesToTrick($files, $fileService, $trick);
            
            $this->addVideoLinksToTrick($videoLinks, $trick);
            
            if (null === $trick->getId()) {
                $trick->setCreatedAt(new \DateTime());
                $this->em->persist($trick);
                $this->addFlash('success', 'Trick créé !');
            } else {
                $trick->setUpdatedAt(new \DateTime());
                $this->addFlash('info', 'Trick mis à jour !');
            }
            
            $this->em->flush();

            return new RedirectResponse($this->generateUrl('app_trick_update', ['slug' => $trick->getSlug()]));
        }

        out:

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
    public function remove(Trick $trick, Request $request)
    {
        $this->em->remove($trick);
        $this->em->flush();

        if ($request->isXmlHttpRequest()) {
            return new JsonResponse(true);
        }
        
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

    /**
     * @Route("/trick/video/remove/{videoLink}")
     *
     * @param VideoLink $videoLink
     * @return JsonResponse
     */
    public function removeVideo(VideoLink $videoLink)
    {
        $this->em->remove($videoLink);
        $this->em->flush();

        return new JsonResponse(true);
    }
}
