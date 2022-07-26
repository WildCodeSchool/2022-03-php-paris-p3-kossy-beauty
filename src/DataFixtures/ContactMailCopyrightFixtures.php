<?php

namespace App\DataFixtures;

use App\Entity\ContactMailCopyright;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ContactMailCopyrightFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $contactMailCopyright = new ContactMailCopyright();
        $manager->persist($contactMailCopyright);

        $manager->flush();
    }
}
