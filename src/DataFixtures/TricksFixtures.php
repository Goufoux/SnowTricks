<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker;
use App\Entity\TrickGroup;
use App\Entity\Trick;
use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class TricksFixtures extends Fixture
{
    const DEFAULT_PASSWORD = 'password';
    
    private $faker;
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
        $this->faker = Faker\Factory::create('fr_FR');
    }
    
    public function load(ObjectManager $manager)
    {
        $this->createUsers($manager);
        $this->createGroups($manager);

        for ($i = 1; $i < 100; $i++) {
            $trick = new Trick();
            $trick->setCreatedAt(new \DateTime());
            $trick->setAuthor($this->getReference('User_'.rand(1, 49)));
            $trick->setTrickGroup($this->getReference('TrickGroup_'.rand(1, 29)));
            $trick->setName($this->faker->sentence(2));
            $trick->setDescription($this->faker->sentence(50));
            $manager->persist($trick);
        }

        $manager->flush();
    }

    private function createGroups(ObjectManager $manager)
    {
        for ($i = 1; $i < 30; $i++) {
            $trickGroup = new TrickGroup();
            $trickGroup->setLabel($this->faker->sentence(3));
            $trickGroup->setCreatedAt(new \DateTime());
            $this->addReference('TrickGroup_'.$i, $trickGroup);
            $manager->persist($trickGroup);
        }
    }

    private function createUsers(ObjectManager $manager)
    {
        $specialUser = new User();
        $specialUser->setName('Roussel');
        $specialUser->setFirstName('Quentin');
        $specialUser->setEmail('quentin.rsoussel@genarkys.fr');
        $specialUser->setCreatedAt(new \DateTime());
        $encodedPassword = $this->encoder->encodePassword($specialUser, self::DEFAULT_PASSWORD);
        $specialUser->setPassword($encodedPassword);
        $specialUser->setActive(true);
        $specialUser->setRoles(["ROLE_ADMIN"]);
        $manager->persist($specialUser);

        for ($i = 1; $i < 50; $i++) {
            $user = new User();
            $user->setName($this->faker->name);
            $user->setFirstName($this->faker->firstName);
            $user->setEmail($this->faker->email);
            $user->setCreatedAt(new \DateTime());
            $encodedPassword = $this->encoder->encodePassword($user, self::DEFAULT_PASSWORD);
            $user->setPassword($encodedPassword);
            $user->setActive(true);
            $manager->persist($user);
            $this->addReference('User_'.$i, $user);
        }
    }
}
