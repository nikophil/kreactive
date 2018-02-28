<?php

namespace App\DataFixtures\ORM;

use App\Entity\Movie;
use App\Entity\User;
use App\Manager\UserManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class UserFixtures
 */
class UserFixtures extends Fixture
{
    /** @var UserManager */
    private $userManager;

    public function __construct(UserManager $userManager)
    {
        $this->userManager = $userManager;
    }

    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     *
     * @throws \Exception
     */
    public function load(ObjectManager $manager)
    {
        $users = [
            'user1',
            'user2',
            'user3',
        ];

        foreach ($users as $userData) {
            /** @var User $user */
            $user = (new User())
                ->setEmail(sprintf('%s@gmail.com', $userData))
                ->setFirstName($userData)
                ->setLastName($userData)
                ->setPlainPassword($userData);

            for ($i=0 ; $i<rand(0, 10) ; $i++) {
                /** @var Movie $movie */
                $movie = $this->getReference('movie-' . rand(0, 9));

                $user->addMovie($movie);
            }

            $this->userManager->updatePassword($user);

            $manager->persist($user);
        }

        $manager->flush();

        return;
    }

    /**
     * @return array
     */
    public function getDependencies()
    {
        return [
            MovieFixtures::class,
        ];
    }
}
