<?php

declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:create:admin')]
class CreateAdminCommand extends Command
{
    public function __construct(private UserCreator $userCreator)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $this->userCreator->createUserFormInput($input, ['ROLE_USER', 'ROLE_ADMIN']);
        } catch (\Exception $e) {
            echo($e->getMessage() . "\n");

            return Command::FAILURE;
        }

        echo("User created.\n");
        return Command::SUCCESS;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('username', InputArgument::REQUIRED, 'Input username.')
            ->addArgument('password', InputArgument::REQUIRED, 'Input password.');
    }
}