<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:users:create',
    description: 'Create a user entry',
)]

class UserCreateCommand extends Command
{
    private const ALLOWED_ENVS = ['dev', 'test'];

    private UserPasswordHasherInterface $passwordHasher;
    private EntityManagerInterface $entityManager;

    public function __construct(
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager
    )
    {
        $this->passwordHasher = $passwordHasher;
        $this->entityManager = $entityManager;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('email', InputArgument::REQUIRED, 'User email')
            ->addArgument('password', InputArgument::REQUIRED, 'Password (plain text)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $env = getenv('APP_ENV') || 'dev';
        if (!in_array($env, self::ALLOWED_ENVS)) {
            $io->error("Command not allowed in {$env} environment");
            return Command::INVALID;
        }

        $userEmail = $input->getArgument('email');
        $plainPassword = $input->getArgument('password');
        $user = new User();
        $user->setEmail($userEmail);
        $hashedPassword = $this->passwordHasher->hashPassword($user, $plainPassword);
        $user->setPassword($hashedPassword);

        try {
            $this->entityManager->persist($user);
            $this->entityManager->flush();
            $io->success("User {$userEmail} successfully saved!");

            return Command::SUCCESS;
        } catch (\Exception $exception) {
            $io->error("User creation failed!". PHP_EOL . $exception->getMessage());

            return Command::FAILURE;
        }
    }
}