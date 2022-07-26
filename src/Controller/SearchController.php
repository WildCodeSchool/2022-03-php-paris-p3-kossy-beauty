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
            ->setAction($this->generateUrl('handleSearch'))
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
    #[Route('/handleSearch', name: 'handleSearch', methods: ['POST'])]
    public function handleSearch(
        Request $request,
        ServiceRepository $serviceRepository,
        ProviderServiceRepository $provServRepository,
        UserRepository $userRepository,
    ): Response {
        // Search query
        $query = $request->request->all('form')['query'];
        $searchedServices = '';
        if ($query) {
            $searchedServices = $serviceRepository->findServicesByName($query);
        }

        //var_dump($searchedServices); die;

        // Providers linked to the query results
        $provider = $prov = '';

        foreach ($searchedServices as $service) {
            $providers = $provServRepository->findByService($service);
            var_dump($providers);
            die;
            foreach ($providers as $provider) {
                $prov = $userRepository->find($provider);
            }
            var_dump($prov);
            die;
        }
        //$providersInService = $provServRepository->findByService($searchedServices);


        return $this->render('search/index.html.twig', [
            'searchedServices' => $searchedServices,
            'providers' => $providers,
        ]);
    }
}
