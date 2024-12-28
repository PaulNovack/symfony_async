<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Faker\Factory;

class UserFixtures extends Fixture
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        $batchSize = 50;
        for ($i = 0; $i < 300; $i++) {
            $user = new User();
            $user->setFirstName($faker->firstName());
            $user->setLastName($faker->lastName());
            $user->setEmail($faker->unique()->safeEmail());
            $user->setPassword($this->passwordHasher->hashPassword($user, 'password'));
            $user->setRoles(['ROLE_USER']);

            $manager->persist($user);

            if (($i + 1) % $batchSize === 0) {
                $manager->flush();
                $manager->clear(); // Detaches all objects from Doctrine for memory management
                echo "Flushed users up to " . ($i + 1) . "\n";
            }
        }

        $manager->flush(); // Flush remaining users
        $manager->clear();
        echo "All users flushed\n";
    }
}
