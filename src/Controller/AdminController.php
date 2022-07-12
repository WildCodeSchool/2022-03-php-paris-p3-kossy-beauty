<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Entity\User;
use App\Repository\CategoryRepository;
use App\Repository\ServiceRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use App\Repository\UserRepository;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    #[IsGranted('ROLE_SUPER_ADMIN')]
    public function index(
        UserRepository $userRepository,
        CategoryRepository $categoryRepository,
        ServiceRepository $serviceRepository
    ): Response {
        $user = new User();
        $users = $userRepository->findAll();
        $roles = $user->getRoles();


        return $this->render('admin/index.html.twig', [
            'users' => $users,
            'user' => $user,
            'roles' => $roles,
            'categories' => $categoryRepository->findAll(),
            'services' => $serviceRepository->findAll()
        ]);
    }
}
