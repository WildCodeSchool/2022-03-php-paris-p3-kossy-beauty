<?php

namespace App\Controller;

use App\Entity\ContactMailCopyright;
use App\Form\ContactMailCopyrightType;
use App\Repository\ContactMailCopyrightRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/contact_mail_and_copyright')]
class ContactMailCopyrightController extends AbstractController
{
    #[Route('/edit/{id}', name: 'app_contact_mail_and_copyright_edit', methods: ['GET', 'POST'])]
    public function editMailAndCopyright(
        Request $request,
        ContactMailCopyright $contactMailCopyright,
        ContactMailCopyrightRepository $contactMailCopyRepo
    ): Response {
        $form = $this->createForm(ContactMailCopyrightType::class, $contactMailCopyright);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $contactMailCopyRepo->add($contactMailCopyright, true);

            return $this->redirectToRoute('app_admin', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('contact_mail_copyright/edit.html.twig', [
            'form' => $form,
            'contactMailCopyright' => $contactMailCopyright
        ]);
    }
}
