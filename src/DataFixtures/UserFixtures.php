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

        // Création d’un utilisateur de type “prestataire”
        for ($i = 0; $i < 50; $i++) {
            $provider = new User();
            $provider->setFirstname('Plop' . $i);
            $provider->setLastname('Plopinette' . $i);
            $provider->setTown('PlopCity');
            $provider->setIsTop(false);
            $provider->setEmail('provider' . $i . '@gmail.com');
            $provider->setIsArchived(false);
            if ($i > 9) {
                $provider->setTelephone('01000000' . $i);
            } else {
                $provider->setTelephone('010000000' . $i);
            }
            $provider->setRoles(['ROLE_PROVIDER']);
            $hashedPassword = $this->passwordHasher->hashPassword(
                $provider,
                'plop'
            );
            $provider->setCompanyName('C dans l\' hair' . $i);
            $provider->setCompanyDescription('Nous sommes ouverts tous les jours de la semaine');
            $provider->setPassword($hashedPassword);
            $manager->persist($provider);
            $this->addReference('provider_' . $i, $provider);
        }

        // Création d’un utilisateur de type “super administrateur”
        $superAdmin = new User();
        $superAdmin->setFirstname('SuperAdmin');
        $superAdmin->setLastname('SuperAdmin');
        $superAdmin->setTown('SuperAdminCity');
        $superAdmin->setIsTop(false);
        $superAdmin->setIsArchived(false);
        $superAdmin->setTelephone('0975318642');
        $superAdmin->setRoles(['ROLE_SUPER_ADMIN']);
        $hashedPassword = $this->passwordHasher->hashPassword(
            $superAdmin,
            'superadmin'
        );
        $superAdmin->setPassword($hashedPassword);
        $manager->persist($superAdmin);

        // Création d’un utilisateur de type “client”
        for ($i = 0; $i < 25; $i++) {
            $user = new User();
            $user->setFirstname('userfirstname' . $i);
            $user->setLastname('userlastname' . $i);
            $user->setEmail('user' . $i . '@gmail.com');
            $user->setTown('UserCity');
            $user->setIsTop(false);
            $user->setIsArchived(false);
            $user->setRoles(['ROLE_USER']);
            if ($i > 9) {
                $user->setTelephone('01200000' . $i);
            } else {
                $user->setTelephone('012000000' . $i);
            }
            $hashedPassword = $this->passwordHasher->hashPassword(
                $user,
                'user'
            );
            $user->setPassword($hashedPassword);
            $manager->persist($user);
        }
        $manager->flush();
    }
}
