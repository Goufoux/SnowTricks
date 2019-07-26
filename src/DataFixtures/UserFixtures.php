<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\User;
use Faker;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Faker\Provider\ka_GE\DateTime;

class UserFixtures extends Fixture
{
    const DEFAULT_PASSWORD = 'password';
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('fr_FR');

        for ($i = 0; $i < 20; $i++) {
            $user = new User();
            $user->setName($faker->name);
            $user->setFirstName($faker->firstName);
            $user->setEmail($faker->email);
            $user->setCreatedAt(new \DateTime());
            $encodedPassword = $this->passwordEncoder->encodePassword($user, self::DEFAULT_PASSWORD);
            $user->setPassword($encodedPassword);
            $manager->persist($user);
        }

        $manager->flush();
    }
}
