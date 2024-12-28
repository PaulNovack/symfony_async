<?php

namespace App\DataFixtures;

use App\DataFixtures\ProductFixtures;
use App\DataFixtures\UserFixtures;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    private $productFixtures;
    private $userFixtures;

    public function __construct(ProductFixtures $productFixtures, UserFixtures $userFixtures)
    {
        $this->productFixtures = $productFixtures;
        $this->userFixtures = $userFixtures;
    }

    public function load(ObjectManager $manager): void
    {
        $this->productFixtures->load($manager);
        $this->userFixtures->load($manager);
    {
        // $product = new Product();
        // $manager->persist($product);

        $manager->flush();
    }
}
