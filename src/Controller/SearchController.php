<?php

namespace App\Controller;

use App\Entity\Service;
use App\Repository\UserRepository;
use App\Service\GeolocationService;
use App\Repository\ServiceRepository;
use App\Repository\ProviderServiceRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
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

    public function searchBar(GeolocationService $geolocationService)
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
            ->add('district', ChoiceType::class, [
                'choices' => [
                    $geolocationService->getDistrict() => $geolocationService->getDistrict(),
                    "Akwa" => "Akwa",
                    "Bali" => "Bali",
                    "Bonamikengue" => "Bonamikengue",
                    "Bessengue" => "Bessengue",
                    "Bonamoudourou" => "Bonamoudourou",
                    "Bona Bekombo" => "Bona Bekombo",
                    "Bonamouti Akwa 2" => "Bonamouti Akwa 2",
                    "Bonadibong" => "Bonadibong",
                    "Bonamouti" => "Bonamouti",
                    "Deïdo" => "Deïdo",
                    "Bonadouma" => "Bonadouma",
                    "Bonanjo" => "Bonanjo",
                    "Bonadoumbé" => "Bonadoumbé",
                    "Bonapriso" => "Bonapriso",
                    "Bonajinjo" => "Bonajinjo",
                    "Bonateki" => "Bonateki",
                    "Bonakouamouang" => "Bonakouamouang",
                    "Bonatene" => "Bonatene",
                    "Bonalembe" => "Bonalembe",
                    "Bonantone" => "Bonantone",
                    "Bonajang" => "Bonajang",
                    "Hydrocarbures" => "Hydrocarbures",
                    "Bonelang" => "Bonelang",
                    "Joss" => "Joss",
                    "Bonoleke" => "Bonoleke",
                    "Koumassi" => "Koumassi",
                    "Bonakeke Akwa" => "Bonakeke Akwa",
                    "Ngodi" => "Ngodi",
                    "Grand Moulin" => "Grand Moulin",
                    "Nkongmondo" => "Nkongmondo",
                    "Nouvelle zone d'Akwa Nord" => "Nouvelle zone d'Akwa Nord",
                    "Nouvelle zone de New-Deido" => "Nouvelle zone de New-Deido"
                ],
                'attr' => [
                    'class' => 'form-select btn btn-light border 
                    border-1 text-nowrap text-dark rounded-0 select-locations'
                ]
            ])
            ->add('recherche', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-light border border-1 text-nowrap text-dark submit-searchbar'
                ]
            ])
            ->getForm();
        //var_dump($geolocationService->getJsonContent()['Countries']['Cameroon']['Cities']['Districts']); die;
        return $this->render('search/searchBar.html.twig', [
            'searchForm' => $form->createView()
        ]);
    }

    /**
     * @param Request $request
     */
    #[Route('/querySearch/{keyword?}/{location?}', name: 'querySearch')]
    public function querySearch(
        Request $request,
        ServiceRepository $serviceRepository,
        ProviderServiceRepository $provServRepository,
        UserRepository $userRepository,
        GeolocationService $geolocationService,
        ?string $keyword,
        ?string $location
    ): Response {

        if (isset($request->request->all('form')['query'])) {
            $query = $request->request->all('form')['query'];
            $district = $request->request->all('form')['district'];
        } elseif ($keyword && $keyword !== null) {
            // Retrieve the keyword and the location from the url
            $query = $keyword;
            if ($location && $location !== null) {
                $district = $location;
            } else {
                // District is defined by the geolocation service to locate the current user
                $district = $geolocationService->getDistrict();
            }
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
                // Check if the provider location is in the district selected
                $validProLocation = $userRepository->findOneBy([
                    'id' => $provider->getProvider(),
                    'district' => $district,
                ]);

                if ($validProLocation) {
                    $pro[] = [
                        $validProLocation,
                        $provider,
                    ];
                }
            }

            // If no provider is found, return a list of providers in others districts
            if (empty($pro)) {
                foreach ($providerServices as $provider) {
                    $othersPro = $userRepository->findOneBy([
                        'id' => $provider->getProvider(),
                    ]);

                    $pro[] = [
                        $othersPro,
                        $provider,
                    ];
                }
            }
        }

        return $this->render('search/index.html.twig', [
            'searchedServices' => $searchedServices,
            'providers' => $pro,
            'keyword' => $query,
            'location' => $district,
        ]);
    }
}
