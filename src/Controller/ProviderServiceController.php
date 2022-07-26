<?php

namespace App\Controller;

use App\Entity\ProviderService;
use App\Entity\Service;
use App\Entity\User;
use App\Form\ProviderServiceType;
use App\Repository\CatalogRepository;
use App\Repository\ProviderServiceRepository;
use App\Repository\ServiceRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

#[Route('/provider/service')]
class ProviderServiceController extends AbstractController
{
    #[Route('/', name: 'app_provider_service_index', methods: ['GET'])]
    public function index(ProviderServiceRepository $provServRepository): Response
    {
        if ($this->getUser() === null) {
            return $this->redirect('../../login');
        } elseif ($this->getUser() != null && $this->getUser()->getRoles()[0] === 'ROLE_USER') {
            return $this->redirect('../../');
        } else {
            return $this->render('provider_service/index.html.twig', [
                'provider_services' => $provServRepository->findAll(),
            ]);
        }
    }

    #[Route('/new', name: 'app_provider_service_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ProviderServiceRepository $provServRepository): Response
    {

        if ($this->getUser() != null && $this->getUser()->getRoles()[0] === 'ROLE_PROVIDER') {
            $providerService = new ProviderService();
            $form = $this->createForm(ProviderServiceType::class, $providerService);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $providerService->setProvider($this->getUser());
                $provServRepository->add($providerService, true);

                return $this->redirectToRoute('app_provider_services_list', [
                    'id' => $this->getUser()->getId()
                ], Response::HTTP_SEE_OTHER);
            }
            return $this->renderForm('provider_service/new.html.twig', [
                'provider_service' => $providerService,
                'form' => $form,
                'id' => $this->getUser()->getId()
            ]);
        } else {
            return $this->redirect('../../');
        }
    }

    #[Route('/{id}', name: 'app_provider_service_show', methods: ['GET'])]
    public function show(ProviderService $providerService): Response
    {
        return $this->render('provider_service/show.html.twig', [
            'provider_service' => $providerService,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_provider_service_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        ProviderService $providerService,
        ProviderServiceRepository $provServRepository
    ): Response {
        $form = $this->createForm(ProviderServiceType::class, $providerService);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $provServRepository->add($providerService, true);

            return $this->redirectToRoute('app_provider_service_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('provider_service/edit.html.twig', [
            'provider_service' => $providerService,
            'form' => $form,
        ]);
    }

    #[Route('/list/{id}', name: 'app_provider_service_show_provider_by_service', methods: ['GET'])]
    public function showProviderByService(
        Service $service,
        ProviderServiceRepository $provServRepository,
    ): Response {
        return $this->render('provider_service/showProviderByService.html.twig', [
            'service' => $service,
            'providerServices' => $provServRepository->findBy(['service' => $service])
        ]);
    }

    #[Route('/{id}/services', name: 'app_provider_services_list', methods: ['GET'])]
    public function showServiceByProvider(
        User $user,
        ProviderServiceRepository $provServRepository,
        CatalogRepository $catalogRepository
    ): Response {
        return $this->render('provider_service/provider_services.html.twig', [
            'services' => $provServRepository->findBy(['provider' => $user]),
            'catalog' => $catalogRepository->findBy(['user' => $user]),
            'user' => $user,
        ]);
    }

    #[Route('/{id}', name: 'app_provider_service_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        ProviderService $providerService,
        ProviderServiceRepository $provServRepository,
    ): Response {
        if ($this->isCsrfTokenValid('delete' . $providerService->getId(), $request->request->get('_token'))) {
            $provServRepository->remove($providerService, true);
        }

        return $this->redirectToRoute(
            'app_provider_services_list',
            ['id' => $this->getUser()->getId()],
            Response::HTTP_SEE_OTHER
        );
    }
}
