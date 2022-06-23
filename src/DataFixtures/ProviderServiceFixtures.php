<?php

namespace App\DataFixtures;

use App\Entity\ProviderService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\DataFixtures\UserFixtures;
use App\DataFixtures\ServiceFixtures;

class ProviderServiceFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 100; $i++) {
            $providerService = new ProviderService();
            $providerService->setProvider($this->getReference('provider_' . rand(0, 49)));
            $providerService->setService($this->getReference('service_' . rand(0, 49)));
            $providerService->setPrice($i + 10);
            $providerService->setDuration($i + 5);
            $manager->persist($providerService);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
            ServiceFixtures::class,
        ];
    }
}
