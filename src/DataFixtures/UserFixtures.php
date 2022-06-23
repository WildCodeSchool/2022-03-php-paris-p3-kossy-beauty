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
        $provider->setCompanyName('Ma coiffure afro');
        $provider->setCompanyDescription('Nous sommes ouverts tous les jours de la semaine');
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

        // Création d’un utilisateur de type user
        $user = new User();
        $user->setFirstname('user');
        $user->setLastname('user');
        $user->setTown('userCity');
        $user->setIsTop(false);
        $user->setIsArchived(false);
        $user->setTelephone('0102030405');
        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            'user'
        );
        $user->setPassword($hashedPassword);
        $manager->persist($user);

        for ($i = 0; $i < 50; $i++) {
            $provider = new User();
            $provider->setFirstname('Plop' . $i);
            $provider->setLastname('Plopinette' . $i);
            $provider->setTown('PlopCity');
            $provider->setIsTop(false);
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

        $manager->flush();
    }
}
