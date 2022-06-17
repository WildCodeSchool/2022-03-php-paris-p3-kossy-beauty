<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // Création d’un utilisateur de type “contributeur” (= auteur)
        $provider = new User();
        $provider->setFirstname('Plop');
        $provider->setLastname('Plop');
        $provider->setTown('PlopCity');
        $provider->setIsTop(false);
        $provider->setIsArchived(false);
        $provider->setTelephone('0123456789');
        $provider->setRoles(['ROLE_PROVIDER']);
        $hashedPassword = $this->passwordHasher->hashPassword(
            $provider,
            'plop'
        );

        $provider->setPassword($hashedPassword);
        $manager->persist($provider);

        // Création d’un utilisateur de type “administrateur”
        $admin = new User();
        $admin->setFirstname('Admin');
        $admin->setLastname('Admin');
        $admin->setTown('AdminCity');
        $admin->setIsTop(false);
        $admin->setIsArchived(false);
        $admin->setTelephone('0987654321');
        $admin->setRoles(['ROLE_ADMIN']);
        $hashedPassword = $this->passwordHasher->hashPassword(
            $admin,
            'admin'
        );
        $admin->setPassword($hashedPassword);
        $manager->persist($admin);

        // Sauvegarde des 2 nouveaux utilisateurs :
        $manager->flush();
    }
}