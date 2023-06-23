<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

const USERS_DATA = [
    [
        'username' => 'user',
        'password' => 'user',
        'userRoles' => ['ROLE_USER']
    ],
    [
        'username' => 'moder',
        'password' => 'moder',
        'userRoles' => ['ROLE_USER', 'ROLE_MODERATOR']
    ],
    [
        'username' => 'admin',
        'password' => 'admin',
        'userRoles' => ['ROLE_USER', 'ROLE_ADMIN']
    ],
];

class AppFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $passwordHasher,
    ) {
    }
    public function load(ObjectManager $manager): void
    {
        foreach (USERS_DATA as $usersData) {
            $user = new User();

            $user->setUsername($usersData['username']);

            $user->setPassword(
                $this->passwordHasher->hashPassword(
                    $user,
                    $usersData['password']
                )
            );

            $user->setRoles($usersData['userRoles']);

            $manager->persist($user);
        }

        $manager->flush();
    }
}
