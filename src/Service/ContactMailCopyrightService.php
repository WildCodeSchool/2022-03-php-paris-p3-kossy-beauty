<?php

namespace App\Service;

use App\Repository\ContactMailCopyrightRepository;

class ContactMailCopyrightService
{
    private $contactMailCopyRepo;

    public function __construct(ContactMailCopyrightRepository $contactMailCopyRepo)
    {
        $this->contactMailCopyRepo = $contactMailCopyRepo;
    }

    public function getAll()
    {
        return $this->contactMailCopyRepo->findAll();
    }
}
