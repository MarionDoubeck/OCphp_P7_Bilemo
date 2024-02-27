<?php

namespace App\DataFixtures;

use App\Entity\Partner;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{

    private $partnerPasswordHasher;

    public function __construct(UserPasswordHasherInterface $partnerPasswordHasher){
        $this->partnerPasswordHasher = $partnerPasswordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $admin = new Partner();
        $admin->setUsername('Bilemo Admin');
        $admin->setRoles(["ROLE_ADMIN"]);
        $admin->setPassword($this->partnerPasswordHasher->hashPassword($admin, "password"));
        $manager->persist($admin);

        $partner = new Partner();
        $partner->setUsername('First Partner');
        $partner->setRoles(["ROLE_USER"]);
        $partner->setPassword($this->partnerPasswordHasher->hashPassword($partner, "password"));
        $manager->persist($partner);

        

    }
}
