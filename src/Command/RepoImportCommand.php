<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use App\Provider\Provider;

#[AsCommand(
    name: 'app:repo:import',
    description: 'Import repositories from organization.',
)]
class RepoImportCommand extends Command
{
    protected function configure(): void
    {
        $this
	    ->addArgument('organization', InputArgument::REQUIRED, 'The name of the organization')
            ->addArgument('provider', InputArgument::REQUIRED, 'The name of the provider to use')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $organizationName = $input->getArgument('organization');
        $providerName = $input->getArgument('provider');

        $provider = Provider::get($providerName);

        $result = $provider->import($organizationName);

        if ($result) {

            $io->success('Import successful!');
            return Command::SUCCESS;

        } else {
            $io->error('Import failed!');
            return Command::FAILURE;
        }

    }
}
