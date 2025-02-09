<?php

namespace App\DataFixtures;

use App\Factory\EditorFactory;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        EditorFactory::new()->createMany(10);
        UserFactory::new()->createMany(10);
        $manager->flush();
    }
}
