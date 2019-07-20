<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use App\Form\PasswordUpdateType;
use App\Form\UpdateUserType;
use Symfony\Component\Form\FormError;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Form\FormInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use App\Service\FileService;
use App\Form\UserType;

class AdminUserController extends AbstractController
{
    private $em;

    public function __construct(ObjectManager $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/admin/user/")
     * @Template()
     */
    public function index()
    {
        $users = $this->em->getRepository(User::class)->findBy([], ['createdAt' => 'DESC']);

        return [
            'users' => $users
        ];
    }

    /**
     * @Route("/admin/user/new")
     * @Template()
     *
     * @param Request $request
     * @return void
     */
    public function new(Request $request, UserPasswordEncoderInterface $encoder)
    {
        $userForm = $this->createForm(UserType::class);

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

            $this->addFlash('success', 'Utilisater ajouté !');

            return new RedirectResponse($this->generateUrl('app_adminuser_index'));
        }

        return [
            'form' => $userForm->createView()
        ];
    }

    /**
     * @Route("/admin/user/update/{user}")
     * @Template()
     *
     * @param User $user
     * @param Request $request
     * @return void
     */
    public function update(User $user, Request $request, UserPasswordEncoderInterface $encoder, FileService $fileService)
    {
        $userForm = $this->createForm(UpdateUserType::class, $user, ['isAdmin' => true, 'hasRoleAdmin' => $user->hasRoleAdmin()]);
        $userForm->handleRequest($request);

        $passwordForm = $this->createForm(PasswordUpdateType::class);
        $passwordForm->handleRequest($request);
        
        if ($userForm->isSubmitted() && $userForm->isValid()) {
            /** @var User $user */
            $user = $userForm->getData();
            $user->setUpdatedAt(new \DateTime());

            if (null !== $user->getFile()) {
                $fileService->uploadFile($user->getFile(), 'avatar_directory');
                $user->setFile(null);
                $user->setAvatar($fileService->getFileName());
            }

            if (true === $userForm->get('roles')->getData() && false === $user->hasRoleAdmin()) {
                $user->setRoles(['ROLE_ADMIN']);
            }

            if (false === $userForm->get('roles')->getData() && true === $user->hasRoleAdmin()) {
                $user->setRoles([]);
            }
            
            $this->em->flush();
            $this->addFlash('success', 'Utilisateur mise à jour !');

            return new RedirectResponse($this->generateUrl('app_adminuser_update', ['user' => $user->getId()]));
        }

        if ($this->verifyPasswordForm($passwordForm, $user, $encoder)) {
            return new RedirectResponse($this->generateUrl('app_adminuser_update', ['user' => $user->getId()]));
        }

        return [
            'user_form' => $userForm->createView(),
            'password_form' => $passwordForm->createView(),
            'user' => $user
        ];
    }

    private function verifyPasswordForm(FormInterface $passwordForm, User $user, UserPasswordEncoderInterface $encoder)
    {
        if ($passwordForm->isSubmitted() && $passwordForm->isValid()) {
            
            $oldPassword = $passwordForm->get('old_password')->getData();

            if (false === password_verify($oldPassword, $user->getPassword())) {
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
     * @Route("/admin/user/remove/{user}")
     *
     * @param User $user
     * @return void
     */
    public function remove(User $user)
    {
        $this->em->remove($user);
        $this->em->flush();

        $this->addFlash('danger', 'Utilisateur supprimé !');

        return new RedirectResponse($this->generateUrl('app_adminuser_index'));
    }
}
