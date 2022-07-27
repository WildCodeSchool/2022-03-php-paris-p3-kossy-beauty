<?php

namespace App\Controller;

use App\Entity\Service;
use App\Repository\UserRepository;
use App\Repository\ServiceRepository;
use App\Repository\ProviderServiceRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SearchController extends AbstractController
{
    #[Route('/search', name: 'app_search')]
    public function index(): Response
    {
        return $this->render('search/index.html.twig', [
            'controller_name' => 'SearchController',
        ]);
    }

    public function searchBar()
    {
        $form = $this->createFormBuilder()
            ->setAction($this->generateUrl('querySearch'))
            ->add('query', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control text-nowrap me-2 rounded-0 input-searchbox',
                    'size' => 40,
                    'placeholder' => 'Entrez un mot-clé'
                ]
            ])
            ->add('recherche', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-success text-nowrap submit-searchbar'
                ]
            ])
            ->getForm();
        return $this->render('search/searchBar.html.twig', [
            'searchForm' => $form->createView()
        ]);
    }

    /**
     * @param Request $request
     */
    #[Route('/querySearch/{keyword?}', name: 'querySearch')]
    public function querySearch(
        Request $request,
        ServiceRepository $serviceRepository,
        ProviderServiceRepository $provServRepository,
        UserRepository $userRepository,
        ?string $keyword
    ): Response {

        if (isset($request->request->all('form')['query'])) {
            $query = $request->request->all('form')['query'];
        } elseif ($keyword && $keyword !== null) {
            $query = $keyword;
        } else {
            return $this->redirectToRoute('home');
        }

        // Search query
        $searchedServices = '';

        // Return services corresponding to the query
        $searchedServices = $serviceRepository->findServicesByName($query);

        // Providers linked to the services found
        $pro = [];

        foreach ($searchedServices as $service) {
            $providerServices = $provServRepository->findByService($service);
            foreach ($providerServices as $provider) {
                $pro[] = [
                    $userRepository->findOneBy(['id' => $provider->getProvider()]),
                    $provider,
                ];
            }
        }

        return $this->render('search/index.html.twig', [
            'searchedServices' => $searchedServices,
            'providers' => $pro,
        ]);
    }
}
