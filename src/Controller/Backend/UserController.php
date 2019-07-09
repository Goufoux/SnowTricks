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
        $userForm = $this->createForm(RegistrationType::class, $user, ['forAdd' => false, 'isAdmin' => true, 'hasRoleAdmin' => $user->hasRoleAdmin()]);
        $userForm->handleRequest($request);

        if ($userForm->isSubmitted() && $userForm->isValid()) {
            /** @var User $user */
            $user = $userForm->getData();
            $user->setUpdatedAt(new \DateTime());
            $password = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($password);
            $this->em->merge($user);
            $this->em->flush();
            $this->addFlash('success', 'user updated!');

            return new RedirectResponse($this->generateUrl('app_admin_user'));
        }

        return $this->render('backend/user/new.html.twig', [
            'form' => $userForm->createView()
        ]);
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
