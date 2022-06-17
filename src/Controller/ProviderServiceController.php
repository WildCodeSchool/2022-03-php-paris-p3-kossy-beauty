<?php

namespace App\Controller;

use App\Entity\ProviderService;
use App\Form\ProviderServiceType;
use App\Repository\ProviderServiceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/provider/service')]
class ProviderServiceController extends AbstractController
{
    #[Route('/', name: 'app_provider_service_index', methods: ['GET'])]
    public function index(ProviderServiceRepository $provServRepository): Response
    {
        return $this->render('provider_service/index.html.twig', [
            'provider_services' => $provServRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_provider_service_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ProviderServiceRepository $provServRepository): Response
    {
        $providerService = new ProviderService();
        $form = $this->createForm(ProviderServiceType::class, $providerService);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $provServRepository->add($providerService, true);

            return $this->redirectToRoute('app_provider_service_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('provider_service/new.html.twig', [
            'provider_service' => $providerService,
            'form' => $form,
        ]);
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

    #[Route('/{id}', name: 'app_provider_service_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        ProviderService $providerService,
        ProviderServiceRepository $provServRepository
    ): Response {
        if ($this->isCsrfTokenValid('delete' . $providerService->getId(), $request->request->get('_token'))) {
            $provServRepository->remove($providerService, true);
        }

        return $this->redirectToRoute('app_provider_service_index', [], Response::HTTP_SEE_OTHER);
    }
}
