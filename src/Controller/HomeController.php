<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Repository\ProviderServiceRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(UserRepository $userRepository, ProviderServiceRepository $provServRepository): Response
    {
        $topProviders = $userRepository->findByIsTop(true);
        $randomTopProvider = array_rand($topProviders, 2);
        $selectedProvider = [];

        foreach ($randomTopProvider as $value) {
            $selectedProvider[] = $provServRepository->findByProvider($topProviders[$value]);
        }

        return $this->render('home/index.html.twig', [
            'selectedProvider' => $selectedProvider,
        ]);
    }
}
