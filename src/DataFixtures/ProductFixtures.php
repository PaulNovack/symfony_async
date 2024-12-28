<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ProductFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        $batchSize = 50;
        for ($i = 0; $i < 500; $i++) {
            $product = new Product();
            $product->setName($faker->words(3, true));
            $product->setDescription($faker->sentence());
            $product->setPrice($faker->randomFloat(2, 1, 1000));
            $product->setQuantity($faker->numberBetween(1, 100));

            $manager->persist($product);

            if (($i + 1) % $batchSize === 0) {
                $manager->flush();
                $manager->clear(); // Detaches all objects from Doctrine for memory management
                echo "Flushed products up to " . ($i + 1) . "\n";
            }
        }

        $manager->flush(); // Flush remaining products
        $manager->clear();
        echo "All products flushed\n";
    }
}
