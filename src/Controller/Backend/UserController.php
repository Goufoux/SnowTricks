<?php

namespace App\Controller\Backend;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use App\Form\RegistrationType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use App\Form\PasswordUpdateType;
use App\Form\UpdateUserType;
use Symfony\Component\Form\FormError;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Form\FormInterface;
use App\Form\MediaType;
use App\Form\AvatarType;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use App\Entity\Avatar;
use Symfony\Component\HttpFoundation\JsonResponse;

class UserController extends AbstractController
{
    private $em;

    public function __construct(ObjectManager $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("admi/admin/user/", name="app_admin_user")
     */
    public function index()
    {
        $users = $this->em->getRepository(User::class)->findBy([], ['created_at' => 'DESC']);

        return $this->render('backend/user/index.html.twig', [
            'users' => $users
        ]);
    }

    /**
     * @Route("/admin/user/new", name="app_admin_user_new")
     *
     * @param Request $request
     * @return void
     */
    public function new(Request $request, UserPasswordEncoderInterface $encoder)
    {
        $userForm = $this->createForm(RegistrationType::class, null, ['isAdmin' => true]);

        // dd($request);
        $userForm->handleRequest($request);

        if ($userForm->isSubmitted() && $userForm->isValid()) {
            /** @var User $user */
            $user = $userForm->getData();
            $user->setCreatedAt(new \DateTime());
            $password = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($password);
            $user->setActive(true);
            if ($userForm->get('roles')->getData() === true) {
                $user->setRoles(['ROLE_ADMIN']);
            }
            $this->em->persist($user);
            $this->em->flush();

            $this->addFlash('success', 'user added!');

            return new RedirectResponse($this->generateUrl('app_admin_user'));
        }

        return $this->render('backend/user/new.html.twig', [
            'form' => $userForm->createView()
        ]);
    }

    /**
     * @Route("/admin/user/update/{user}", name="app_admin_user_update")
     *
     * @param User $user
     * @param Request $request
     * @return void
     */
    public function update(User $user, Request $request, UserPasswordEncoderInterface $encoder)
    {
        $userForm = $this->createForm(UpdateUserType::class, $user, ['isAdmin' => true]);
        $userForm->handleRequest($request);

        $passwordForm = $this->createForm(PasswordUpdateType::class);
        $passwordForm->handleRequest($request);

        if ($userForm->isSubmitted() && $userForm->isValid()) {

            /** @var User $user */
            $user = $userForm->getData();
            
            if (!empty($request->files)) {
                /** @var UploadedFile $avatar */
                $avatar = $request->files->get('update_user')['avatar'];
                $fileName = uniqid().'.'.$avatar->guessClientExtension();
                $user->setAvatar($fileName);
                $avatar->move($this->getParameter('avatar_directory'), $fileName);
            }
            
            $user->setUpdatedAt(new \DateTime());
            $this->em->merge($user);
            $this->em->flush();
            $this->addFlash('success', 'user updated!');

            return new RedirectResponse($this->generateUrl('app_admin_user'));
        }

        if ($this->verifyPasswordForm($passwordForm, $user, $encoder)) {
            return new RedirectResponse($this->generateUrl('app_admin_user_update', ['user' => $user->getId()]));
        }

        return $this->render('backend/user/update.html.twig', [
            'user_form' => $userForm->createView(),
            'password_form' => $passwordForm->createView(),
            'user' => $user
        ]);
    }

    private function uploadAvatar(FormInterface $form, User $user)
    {
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $file */
            $file = $form->get('avatar_src')->getData();
            // dd($form->get('avatar_src'));
            $fs = new Filesystem();
            $mediaName = uniqid();
            $ext = $file->getClientOriginalExtension();
            try {
                $file->move($this->getParameter('avatar_directory'), $mediaName.'.'.$ext);

                if ($user->getAvatar() === null) {
                    $avatar = new Avatar();
                    $avatar->setUser($user);
                } else {
                    $avatar = $user->getAvatar();
                }
                
                $avatar->setAvatarSrc($mediaName.'.'.$ext);

                $user->setAvatar($avatar);

                // $user->getAvatar()->setAvatarSrc($mediaName.'.'.$ext);

                $this->em->merge($user);

                $this->em->flush();

                $this->addFlash('success', 'File uploaded');
            } catch (FileException $e) {
                $this->addFlash('danger', $e->getMessage());
            }
            // dd($file->guessExtension(), $form, $mediaName);
            return true;
        }

        return false;
    }

    private function verifyPasswordForm(FormInterface $passwordForm, User $user, UserPasswordEncoderInterface $encoder)
    {
        if ($passwordForm->isSubmitted() && $passwordForm->isValid()) {
            
            $oldPassword = $passwordForm->get('old_password')->getData();

            if (password_verify($oldPassword, $user->getPassword()) === false) {
                $passwordForm->get('old_password')->addError(new FormError('Mot de passe incorrect'));

                return false;
            }

            $encodedPassword = $encoder->encodePassword($user, $passwordForm->get('password')->getData());
            $user->setPassword($encodedPassword);

            $this->em->merge($user);
            $this->em->flush();

            $this->addFlash('success', 'Données de connexion mise à jour.');
            
            return true;
        }

        return false;
    }

    /**
     * @Route("/admin/user/remove/{user}", name="app_admin_user_remove")
     *
     * @param User $user
     * @return void
     */
    public function remove(User $user)
    {
        $this->em->remove($user);
        $this->em->flush();

        $this->addFlash('danger', 'user has been deleted !');

        return new RedirectResponse($this->generateUrl('app_admin_user'));
    }
}
