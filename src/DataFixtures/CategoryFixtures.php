<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{
    public const CATEGORIES = [
        'Coiffure',
        'Soins du corps',
        'Soins du visage',
        'Make Up',
        'Manucure & Nail design'
    ];

    public function load(ObjectManager $manager)
    {
        foreach (self::CATEGORIES as $key => $categoryName) {
            $category = new Category();
            $category->setName($categoryName);
            $manager->persist($category);
            $catId = $key + 1;
            $this->addReference('category_' . $catId, $category);
        }

        $manager->flush();
    }
}
