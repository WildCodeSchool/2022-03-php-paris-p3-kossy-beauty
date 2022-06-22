<?php

namespace App\Service;

use App\Repository\ServiceRepository;

class ServiceService
{
    private $serviceRepository;

    public function __construct(ServiceRepository $serviceRepository)
    {
        $this->serviceRepository = $serviceRepository;
    }

    public function getServiceByCategoryId(string $categoryId)
    {
        $categoryId = intval($categoryId);
        return $this->serviceRepository->findBy(['category' => $categoryId]);
    }
}
