<?php

namespace App\Service;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class NotificationService extends AbstractController
{
    public function notification(): bool
    {
        if (!$this->getUser()) {
            return false;
        }

        $conversations = $this->getUser()->getConversations();

        foreach ($conversations as $conversation) {
            if ($conversation->getLastMessage()->getAuthor() !== $this->getUser()) {
                return true;
            }
        }
        return false;
    }

    public function toggleNotif(): bool
    {
        if ($this->notification()) {
            return false;
        }
        return true;
    }
}
