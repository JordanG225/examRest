<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;


class UserFixtures extends Fixture
{
    private $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager)
    {
        $user1 = new User();
        $user1->setEmail("jordan");
        $user1->setRoles(["ROLE_ADMIN"]);
        $user1->setPassword($this->hasher->hashPassword($user1, "admin"));

        $user2 = new User();
        $user2->setEmail("yahnn");
        $user2->setRoles(["ROLE_USER"]);
        $user2->setPassword($this->hasher->hashPassword($user2, "user"));

        $manager->persist($user1);
        $manager->persist($user2);
       
        $manager->flush();
    }
}
