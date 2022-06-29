<?php

namespace App\Service;

use App\Repository\UserRepository;

class ProviderService
{
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getProviderById(int $id)
    {
        return $this->userRepository->findById($id);
    }
}
