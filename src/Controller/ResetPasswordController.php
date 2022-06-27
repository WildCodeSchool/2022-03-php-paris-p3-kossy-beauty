<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ChangePasswordFormType;
use App\Form\ResetPasswordRequestFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\ResetPassword\Controller\ResetPasswordControllerTrait;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;
use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordToken as ResetPasswordToken;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use DateTime;

#[Route('/reset-password')]
class ResetPasswordController extends AbstractController
{
    use ResetPasswordControllerTrait;

    private ResetPasswordHelperInterface $resetPasswordHelper;
    private EntityManagerInterface $entityManager;

    public function __construct(
        ResetPasswordHelperInterface $resetPasswordHelper,
        EntityManagerInterface $entityManager
    ) {
        $this->resetPasswordHelper = $resetPasswordHelper;
        $this->entityManager = $entityManager;
    }

    /**
     * Display & process form to request a password reset.
     */
    #[Route('', name: 'app_forgot_password_request')]
    public function request(
        Request $request,
        TranslatorInterface $translator
    ): Response {
        $form = $this->createForm(ResetPasswordRequestFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return $this->processSendingPasswordResetMessage(
                $form->get('telephone')->getData(),
                $translator,
                $request
            );
        }

        return $this->render('reset_password/request.html.twig', [
            'requestForm' => $form->createView(),
        ]);
    }

    /**
     * Send the reset password email / Whatsapp message
     */
    private function processSendingPasswordResetMessage(
        string $telephoneFormData,
        TranslatorInterface $translator,
        Request $request
    ): RedirectResponse {
        $user = $this->entityManager->getRepository(User::class)->findOneBy([
            'telephone' => $telephoneFormData,
        ]);

        // Do not reveal whether a user account was found or not.
        if (!$user) {
            return $this->redirectToRoute('app_check_email');
        }

        try {
            $resetToken = $this->resetPasswordHelper->generateResetToken($user);
        } catch (ResetPasswordExceptionInterface $e) {
            // If you want to tell the user why a reset email was not sent, uncomment
            // the lines below and change the redirect to 'app_forgot_password_request'.
            // Caution: This may reveal if a user is registered or not.
            //
            // $this->addFlash('reset_password_error', sprintf(
            //     '%s - %s',
            //     $translator->trans(
            //         ResetPasswordExceptionInterface::MESSAGE_PROBLEM_HANDLE,
            //         [],
            //         'ResetPasswordBundle'
            //     ),
            //     $translator->trans($e->getReason(), [], 'ResetPasswordBundle')
            // ));
            return $this->redirectToRoute('app_check_email');
        }

        // We Update the user object with the most updated data (the last valid hashedToken)
        $this->entityManager->refresh($user);
        $this->sendToWhatsapp($user, $resetToken, $request);

        // Store the token object in session for retrieval in check-email route.
        $this->setTokenObjectInSession($resetToken);

        return $this->redirectToRoute('app_check_email');
    }

    private function sendToWhatsapp(User $user, ResetPasswordToken $resetToken, Request $request)
    {
        //$session = $this->requestStack->getSession();
        // Call to the Meta API to send the message to the user Whatsapp account
        $apiMetaUrl = 'https://graph.facebook.com/v13.0/104765845620729/messages';
        // phpcs:ignore -- The token access of the API can't be splitted or shortened
        $apiTokenAccess = 'EAAJ4ewfbeNwBAC8YDWzvZCubaLd66OiUJH3Tgh6p4kGBnl6wW5ZC0uPJEnFcSnY7OfnXNMEX4kEppQNyyueGkKjRy4Mizo3AJK3rXAMVMh9AcNoB6RSCRO2XnyJdGvcJzvXSTQwjPdoKGPzeZBYtojfaoMYPzbRTVKUchlK3xldmQwjjeDPgBYbJZCkeIKYce6f93ZC4LXwZDZD';
        $userTelephone = '33' . substr($user->getTelephone(), 1);
        $userFirstname = $user->getFirstname();
        $resetTokenCreatedAt = new DateTime();
        $resetTokenExpiresAt = $resetToken->getExpiresAt();
        // https://www.php.net/manual/en/dateinterval.format.php
        $resetTokenExpiration = $resetTokenCreatedAt
            ->diff($resetTokenExpiresAt)
            ->format('%i minutes.');
        $urlResetToken = $request->getBaseUrl() . $this->generateUrl('app_reset_password', [
            'token' => $resetToken->getToken()
        ]);

        // For debug only
        // $userTelephone = '33645417754';

        // Data to pass to the API
        $data = array();
        $data = [
            "messaging_product" => "whatsapp",
            "to" => $userTelephone,
            "type" => "template",
            "template" => [
                "name" => "reset_password",
                "language" => [
                    "code" => "fr"
                ],
                "components" => [
                    [
                        "type" => "body",
                        "parameters" => [
                            [
                                "type" => "text",
                                "text" => $userFirstname
                            ],
                            [
                                "type" => "text",
                                "text" => $urlResetToken
                            ],
                            [
                                "type" => "text",
                                "text" => $resetTokenExpiration
                            ]
                        ]
                    ]
                ]
            ]
        ];

        // We convert the data to Json format before to send it to the API
        $data = json_encode($data);

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $apiMetaUrl);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

        $headers = array(
            'Content-Type: application/json',
            "Accept: application/json",
            'Authorization: Bearer ' . $apiTokenAccess,
        );
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        curl_exec($curl);
        // Error handler
        // Please don't delete theses lines
        // if (curl_errno($curl)) {
        //     echo 'Error:' . curl_error($curl);
        // }
        curl_close($curl);
    }

    /**
     * Confirmation page after a user has requested a password reset.
     */
    /**
     * This will suppress IfStatementAssignment
     * warnings in this method
     *
     * @SuppressWarnings(PHPMD.IfStatementAssignment)
     */
    #[Route('/check-email', name: 'app_check_email')]
    public function checkEmail(): Response
    {
        // Generate a fake token if the user does not exist or someone hit this page directly.
        // This prevents exposing whether or not a user was found with the given email address or not
        if (null === ($resetToken = $this->getTokenObjectFromSession())) {
            $resetToken = $this->resetPasswordHelper->generateFakeResetToken();
        }

        return $this->render('reset_password/check_email.html.twig', [
            'resetToken' => $resetToken,
        ]);
    }

    /**
     * Validates and process the reset URL that the user clicked in their email.
     */
    #[Route('/reset/{token}', name: 'app_reset_password')]
    public function reset(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        TranslatorInterface $translator,
        string $token = null
    ): Response {
        if ($token) {
            // We store the token in session and remove it from the URL, to avoid the URL being
            // loaded in a browser and potentially leaking the token to 3rd party JavaScript.
            $this->storeTokenInSession($token);

            return $this->redirectToRoute('app_reset_password');
        }

        $token = $this->getTokenFromSession();
        if (null === $token) {
            throw $this->createNotFoundException('No reset password token found in the URL or in the session.');
        }

        try {
            $user = $this->resetPasswordHelper->validateTokenAndFetchUser($token);
        } catch (ResetPasswordExceptionInterface $e) {
            $this->addFlash('reset_password_error', sprintf(
                '%s - %s',
                $translator->trans(
                    ResetPasswordExceptionInterface::MESSAGE_PROBLEM_VALIDATE,
                    [],
                    'ResetPasswordBundle'
                ),
                $translator->trans($e->getReason(), [], 'ResetPasswordBundle')
            ));

            return $this->redirectToRoute('app_forgot_password_request');
        }

        // The token is valid; allow the user to change their password.
        $form = $this->createForm(ChangePasswordFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // A password reset token should be used only once, remove it.
            $this->resetPasswordHelper->removeResetRequest($token);

            // Encode(hash) the plain password, and set it.
            $encodedPassword = $userPasswordHasher->hashPassword(
                $user,
                $form->get('plainPassword')->getData()
            );

            $user->setPassword($encodedPassword);
            $this->entityManager->flush();

            // The session is cleaned up after the password has been changed.
            $this->cleanSessionAfterReset();

            return $this->redirectToRoute('login');
        }

        return $this->render('reset_password/reset.html.twig', [
            'resetForm' => $form->createView(),
        ]);
    }
}
