<?php

namespace App\Controller;

use App\Service\WhatsappService;
use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
    private WhatsappService $whatsapp;
    private VerifyEmailHelperInterface $verifyTelHelper;
    private EntityManagerInterface $entityManager;

    public function __construct(
        WhatsappService $whatsapp,
        VerifyEmailHelperInterface $helper,
        EntityManagerInterface $entityManager
    ) {
        $this->whatsapp = $whatsapp;
        $this->verifyTelHelper = $helper;
        $this->entityManager = $entityManager;
    }

    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher
    ): Response {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setRoles(["ROLE_USER"]);
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            // generate a signed url and message it to the user
            $content = $this->messageDetails('app_verify_email', $user);
            $this->whatsapp->sendMessage(
                $user,
                'verify_telephone_template',
                $content,
                $request
            );

            $this->addFlash(
                'success',
                'Votre compte a été crée avec succès mais n\'est pas encore activé. 
                Pour vous connecter, vous devez dabord confirmer votre numéro de téléphone
                via le message Whatsapp qui vous a été envoyé.'
            );

            return $this->redirectToRoute('login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * Check the email confirmation link submitted by the user
     */
    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(
        Request $request,
        TranslatorInterface $translator,
        UserRepository $userRepository
    ): Response {
        $id = $request->get('id');

        if (null === $id) {
            return $this->redirectToRoute('app_register');
        }

        $user = $userRepository->find($id);

        if (null === $user) {
            return $this->redirectToRoute('app_register');
        }

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));

            return $this->redirectToRoute('app_register');
        }

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        $this->addFlash(
            'success',
            'Votre email a été vérifié ! Votre compte est désormais activé. 
            Vous pouvez à présent vous connecter avec vos identifiants.'
        );

        return $this->redirectToRoute('login');
    }

    /**
     * Details of the message (url, experation time) returned as an array
     */
    public function messageDetails(
        string $verifyTelRoute,
        $user
    ): array {
        $signatureComponents = $this->verifyTelHelper->generateSignature(
            $verifyTelRoute,
            $user->getId(),
            $user->getTelephone(),
            ['id' => $user->getId()]
        );

        $context = [];
        $context['urlVerification'] = $signatureComponents->getSignedUrl();
        $context['expiresAtMessageKey'] = $signatureComponents->getExpirationMessageKey();
        $context['expirationMessageData'] = $signatureComponents->getExpirationMessageData();
        $context['expiration'] = $signatureComponents->getExpiresAt();

        return $context;
    }

    /**
     * User can request again a new Whatsapp message
     */
    #[Route('/verify/resend', name: 'app_verify_resend_email')]
    public function resendVerifyEmail()
    {
        return $this->render('registration/resend_verify_email.html.twig');
    }

    /**
     * @throws VerifyEmailExceptionInterface
     */
    public function handleEmailConfirmation(Request $request, $user): void
    {
        $this->verifyTelHelper->validateEmailConfirmation(
            $request->getUri(),
            $user->getId(),
            $user->getTelephone()
        );

        $user->setIsVerified(true);

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}
