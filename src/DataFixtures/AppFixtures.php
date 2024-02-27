<?php

namespace App\DataFixtures;

use App\Entity\Consumer;
use App\Entity\Partner;
use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Faker;

class AppFixtures extends Fixture
{

    private $partnerPasswordHasher;

    public function __construct(UserPasswordHasherInterface $partnerPasswordHasher){
        $this->partnerPasswordHasher = $partnerPasswordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create('fr_FR');

        //partners
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

        //mobiles
        $brands=['Apple', 'Samsung', 'Huawei', 'Xiaomi', 'Google', 'Sony', 'Oppo', 'OnePlus', 'Motorola', 'Vivo'];
        for ($i = 1; $i < 30; $i++){
            $mobile = new Product;
            $mobile->setModel($faker->unique()->word);
            $mobile->setBrand($brands[array_rand($brands)]);
            $mobile->setPrice($faker->randomFloat(2, 100, 800));
            $mobile->setDescription($faker->text(200));
            $mobile->setCreatedAt(new \DateTimeImmutable());
            $manager->persist($mobile);
        }

        //consumers
        for ($i = 1; $i < 30; $i++){
            $consumer = new Consumer;
            $consumer->setFirstName($faker->firstName);
            $consumer->setLastName($faker->lastName);
            $consumer->setEmail($faker->email);
            $consumer->setAdress($faker->streetAddress);
            $consumer->setPostCode($faker->postcode);
            $consumer->setCity($faker->city);
            $consumer->setPartner($partner);
            $manager->persist($consumer);
        }

        $manager->flush();
    }
}
