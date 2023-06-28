<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserCreator
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
        private EntityManagerInterface      $entityManager,
    )
    {
    }

    public function createUserFormInput(InputInterface $input, array $roles): void
    {
        $username = $input->getArgument('username');
        $password = $input->getArgument('password');

        $user = new User();

        $user->setUsername($username);

        $user->setPassword(
            $this->passwordHasher->hashPassword(
                $user,
                $password,
            )
        );

        $user->setRoles($roles);

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}
