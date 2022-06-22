<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

#[Route('/user')]
class UserController extends AbstractController
{
    #[Route('/', name: 'app_user_index', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    // #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    // public function new(Request $request, UserRepository $userRepository): Response
    // {
    //     $user = new User();
    //     $form = $this->createForm(UserType::class, $user);
    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $userRepository->add($user, true);

    //         return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    //     }

    //     return $this->renderForm('user/new.html.twig', [
    //         'user' => $user,
    //         'form' => $form,
    //     ]);
    // }

    #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function show(User $user): Response
    {
        $currentUser = $this->getUser();
        if (!$this->hasProfileAccess($user, $currentUser)) {
            return $this->redirectToRoute('home', [], Response::HTTP_SEE_OTHER);
        }
        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            $role = 'admin';
        } elseif (in_array('ROLE_PROVIDER', $user->getRoles())) {
            $role = 'provider';
        } else {
            $role = 'utilisateur';
        }
        return $this->render('user/show.html.twig', [
            'user'  => $user,
            'role'  => $role,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function edit(Request $request, User $user, UserRepository $userRepository): Response
    {
        $currentUser = $this->getUser();
        if (!$this->hasProfileAccess($user, $currentUser)) {
            return $this->redirectToRoute('home', [], Response::HTTP_SEE_OTHER);
        }
        $form = $this->createForm(
            UserType::class,
            $user,
            [
                'role' => $currentUser->getRoles()
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userRepository->add($user, true);

            return $this->redirectToRoute('app_user_show', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, UserRepository $userRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $userRepository->remove($user, true);
        }

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }

    // Check if an user is authorized to access to a profil page
    public function hasProfileAccess(User $user, $currentUser): bool
    {
        $userId = $user->getId();
        $currentUserId = $currentUser->getId();
        $currentUserRole = $currentUser->getRoles();
        if (
            !in_array('ROLE_ADMIN', $currentUserRole) &&
            $currentUserId !== $userId
        ) {
            return false;
        }
        return true;
    }
}
