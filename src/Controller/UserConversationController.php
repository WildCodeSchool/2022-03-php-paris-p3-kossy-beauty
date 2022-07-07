<?php

namespace App\Controller;

use App\Entity\Conversation;
use App\Entity\Message;
use App\Entity\User;
use App\Form\MessageType;
use App\Repository\ConversationRepository;
use App\Repository\MessageRepository;
use App\Service\NotificationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserConversationController extends AbstractController
{
    #[Route('/my-conversations', name: 'app_my_conversations')]
    public function index(): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('login', []);
        }

        return $this->render('user_conversation/index.html.twig', [
            'conversations' => $this->getUser()->getConversations()
        ]);
    }

    #[Route('/send-message-to/{id}/about{service}', name: 'app_create_conversation')]
    public function newConversation(
        User $recipient,
        string $service,
        Request $request,
        ConversationRepository $convRepository,
        MessageRepository $messageRepository
    ): Response {

        if (!$this->getUser()) {
            return $this->redirectToRoute('login', []);
        }

        if (isset($this->getUser()->getRoles()[1])) {
            return $this->redirectToRoute('login', []);
        }

        $convsCurrentUser = $this->getUser()->getConversations();

        foreach ($convsCurrentUser as $conversation) {
            if ($conversation->getUsers()->contains($recipient)) {
                return $this->redirectToRoute('app_conversation', [
                    'id' => $conversation->getId(),
                ]);
            }
        }

        $conversation = new Conversation();

        $conversation->addUser($recipient);
        $conversation->addUser($this->getUser());
        $conversation->setSubject($service);

        $message = new Message();

        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $convRepository->add($conversation, true);

            $message->setAuthor($this->getUser());
            $message->setConversation($conversation);

            $messageRepository->add($message, true);

            $conversation->setLastMessage($message);
            $convRepository->add($conversation, true);

            return $this->redirectToRoute('app_conversation', [
                'id' => $conversation->getId(),
            ]);
        }

        return $this->render('user_conversation/new-conversation.html.twig', [
            'form' => $form->createView(),
            'recipient' => $recipient
        ]);
    }

    #[Route('/conversation/{id}', name: 'app_conversation')]
    public function conversation(
        Conversation $conversation,
        Request $request,
        MessageRepository $messageRepository,
        ConversationRepository $convRepository,
        NotificationService $notificationService,
    ): Response {
        $convsCurrentUser = $this->getUser()->getConversations();
        if (!$convsCurrentUser->contains($conversation)) {
            return $this->redirectToRoute('app_my_conversations');
        }

        if ($notificationService->notification()) {
            $notificationService->toggleNotif();
            var_dump($notificationService->toggleNotif());
            die;
        }

        $message = new Message();

        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $message->setAuthor($this->getUser());
            $message->setConversation($conversation);
            $messageRepository->add($message, true);

            $conversation->setLastMessage($message);
            $convRepository->add($conversation, true);

            return $this->redirectToRoute('app_conversation', [
                'id' => $conversation->getId(),
            ]);
        }
        return $this->render('user_conversation/conversation.html.twig', [
            'conversation' => $conversation,
            'messages' => $conversation->getMessages(),
            'form' => $form->createView()
        ]);
    }
}
