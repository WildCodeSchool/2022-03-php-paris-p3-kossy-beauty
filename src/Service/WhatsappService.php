<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;
use DateTime;

class WhatsappService
{
    private VerifyEmailHelperInterface $verifyTelHelper;
    private EntityManagerInterface $entityManager;

    public function __construct(
        VerifyEmailHelperInterface $helper,
        EntityManagerInterface $manager
    ) {
        $this->verifyTelHelper = $helper;
        $this->entityManager = $manager;
    }

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

    public function sendMessage(User $user, $apiTemplateName, array $message, Request $request)
    {
        // Call to the Meta API to send the message to the user Whatsapp account
        $apiMetaUrl = 'https://graph.facebook.com/v13.0/104765845620729/messages';
        // phpcs:ignore -- The token access of the API can't be splitted or shortened
        $apiTokenAccess = 'EAAJ4ewfbeNwBAC8YDWzvZCubaLd66OiUJH3Tgh6p4kGBnl6wW5ZC0uPJEnFcSnY7OfnXNMEX4kEppQNyyueGkKjRy4Mizo3AJK3rXAMVMh9AcNoB6RSCRO2XnyJdGvcJzvXSTQwjPdoKGPzeZBYtojfaoMYPzbRTVKUchlK3xldmQwjjeDPgBYbJZCkeIKYce6f93ZC4LXwZDZD';
        $userTelephone = '33' . substr($user->getTelephone(), 1);
        $userFirstname = $user->getFirstname();
        $messageCreatedAt = new DateTime();
        $messageExpiresAt = $message['expiration'];
        // https://www.php.net/manual/en/dateinterval.format.php
        $messageExpiration = $messageCreatedAt
            ->diff($messageExpiresAt)
            ->format('%i minutes.');
        $urlVerification = $message['urlVerification'];
        // $urlVerification = $request->getBaseUrl() . $this->generateUrl('app_reset_password', [
        //     'token' => $resetToken->getToken()
        // ]);

        // For debug only
        // $userTelephone = '33645417754';

        // Data to pass to the API
        $data = array();
        $data = [
            "messaging_product" => "whatsapp",
            "to" => $userTelephone,
            "type" => "template",
            "template" => [
                "name" => $apiTemplateName,
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
                                "text" => $urlVerification
                            ],
                            [
                                "type" => "text",
                                "text" => $messageExpiration
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
     * @throws VerifyEmailExceptionInterface
     */
    public function handleEmailConfirmation(Request $request, $user): void
    {
        $this->verifyTelHelper->validateEmailConfirmation($request->getUri(), $user->getId(), $user->getTelephone());

        $user->setIsVerified(true);

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}
