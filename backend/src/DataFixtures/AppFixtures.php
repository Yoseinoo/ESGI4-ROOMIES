<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setEmail('user@example.com');
        $hashedPassword = $this->passwordHasher->hashPassword($user, 'password123');
        $user->setPassword($hashedPassword);
        $user->setRoles(['ROLE_USER']);

        $manager->persist($user);

        $user2 = new User();
        $user2->setEmail('test@test.fr');
        $hashedPassword = $this->passwordHasher->hashPassword($user2, 'test');
        $user2->setPassword($hashedPassword);
        $user2->setRoles(['ROLE_USER']);

        $manager->persist($user2);

        $manager->flush();
    }
}
