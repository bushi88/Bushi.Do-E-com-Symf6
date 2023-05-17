<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $passwordEncoder)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        $admin = new User();
        $admin->setEmail('admin@admin.fr');
        $admin->setPassword(
            $this->passwordEncoder->hashPassword($admin, 'admin')
        );
        $admin->setUsername('Bushi88');
        $admin->setLastname('CHERCHARI');
        $admin->setFirstname('Djamel');
        $admin->setRoles(['ROLE_ADMIN']);
        $manager->persist($admin);


        for ($usr = 1; $usr <= 9; $usr++) {
            $user = new User();
            $user->setEmail($faker->email);
            $user->setPassword(
                $this->passwordEncoder->hashPassword($user, 'password')
            );
            $user->setUsername($faker->unique()->userName);
            $user->setLastname($faker->lastName());
            $user->setRoles(['ROLE_USER']);
            $user->setFirstName($faker->firstName());
            $manager->persist($user);
        }

        $manager->flush();
    }
}