<?php

namespace App\Service;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class NotificationService extends AbstractController
{
    public function notification(): bool
    {
        if (!$this->getUser()) {
            return true;
        }

        $conversations = $this->getUser()->getConversations();

        foreach ($conversations as $conversation) {
            if ($conversation->getLastMessage()->getAuthor() !== $this->getUser()) {
                if ($conversation->getLastMessage()->isIsSeen() === false) {
                    return false;
                }
                return true;
            }
        }
        return true;
    }
}
