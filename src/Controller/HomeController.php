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
        // Display 2 top providers
        $topProviders = $userRepository->findByIsTop(true, null, 2);
        $selectedProvider = [];

        foreach ($topProviders as $topProvider) {
            // Display only the first service for each provider
            $selectedProvider[] = $provServRepository->findByProvider($topProvider, null, 1);
        }

        return $this->render('home/index.html.twig', [
            'selectedProvider' => $selectedProvider,
        ]);
    }
}
