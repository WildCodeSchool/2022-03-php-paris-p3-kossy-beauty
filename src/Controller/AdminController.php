<?php

namespace App\Controller;

use App\Entity\SuperAdmin;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Entity\User;
use App\Form\SuperAdminType;
use App\Repository\CategoryRepository;
use App\Repository\ContactMailCopyrightRepository;
use App\Repository\ServiceRepository;
use App\Repository\SuperAdminRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    #[IsGranted('ROLE_SUPER_ADMIN')]
    public function index(
        UserRepository $userRepository,
        CategoryRepository $categoryRepository,
        ServiceRepository $serviceRepository,
        ContactMailCopyrightRepository $contactMailCopyRepo,
    ): Response {
        $user = new User();
        $users = $userRepository->findAll();
        $roles = $user->getRoles();

        $contactMailCopyright = $contactMailCopyRepo->findAll();

        return $this->render('admin/index.html.twig', [
            'users' => $users,
            'user' => $user,
            'roles' => $roles,
            'categories' => $categoryRepository->findAll(),
            'services' => $serviceRepository->findAll(),
            'contactMailCopyright' => $contactMailCopyright[0],
            'contactMail' => $contactMailCopyright[0]->getEmail(),
            'copyright' => $contactMailCopyright[0]->getCopyright()
        ]);
    }

    #[Route('/is-top/{id}', name: 'app_admin_is_top', methods: ['GET'])]
    public function toggleIsTop(User $user, UserRepository $userRepository): Response
    {
        $status = $user->isIsTop();
        if ($status) {
            $user->setIsTop(false);
        } else {
            $user->setIsTop(true);
        }

        $userRepository->add($user, true);

        return $this->redirectToRoute('app_admin', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/is-admin/{id}', name: 'app_admin_is_admin', methods: ['GET'])]
    public function toggleIsAdmin(User $user, UserRepository $userRepository): Response
    {
        $status = $user->getRoles();
        if (!in_array('ROLE_ADMIN', $status)) {
            $user->setRoles(['ROLE_ADMIN']);
        } else {
            $status = ['ROLE_USER'];
            $user->setRoles($status);
        }

        $userRepository->add($user, true);

        return $this->redirectToRoute('app_admin', [], Response::HTTP_SEE_OTHER);
    }
}
