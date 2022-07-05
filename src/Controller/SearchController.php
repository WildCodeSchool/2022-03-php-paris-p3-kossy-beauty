<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

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
                    'class' => 'form-control text-nowrap me-2 rounded-0',
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
    #[Route('/handleSearch', name: 'handleSearch')]
    public function handleSearch(Request $request, ServiceRepository $serviceRepository)
    {
        $query = $request->request->get('form')['query'];
        if($query) {
            $services = $serviceRepository->findServicesByName($query);
        }
        return $this->render('search/index.html.twig', [
            'services' => $services
        ]);
    }
}
