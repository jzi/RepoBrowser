<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use Doctrine\ORM\EntityManagerInterface;

use App\Provider\Provider;
use App\Provider\RepoResult;
use App\Entity\CodeRepo;
use App\Entity\Organization;

#[AsCommand(
    name: 'app:repo:import',
    description: 'Import repositories from organization.',
)]
class RepoImportCommand extends Command
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
        parent::__construct();
    }
    
    protected function configure(): void
    {
        $this
	    ->addArgument('organization', InputArgument::REQUIRED, 'The name of the organization')
            ->addArgument('provider', InputArgument::REQUIRED, 'The name of the provider to use')
            ->addOption('username', null, InputOption::VALUE_REQUIRED, 'username')
            ->addOption('password', null, InputOption::VALUE_REQUIRED, 'password')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $result = false;
        $io = new SymfonyStyle($input, $output);
        $organizationName = $input->getArgument('organization');
        $providerName = $input->getArgument('provider');

        $provider = Provider::get($providerName);

        if ($provider instanceof \App\Provider\IAuthable) {
            $username = $input->getOption('username');
            $password = $input->getOption('password');
            if ($username && $password) {
                $provider->setCredentials($username, $password);
            }
        }

        $repositories = $provider->import($organizationName);

        foreach ($repositories as $repository) {

            $repository->calculateTrustScore();

            $ormOrgRepository = $this->entityManager->getRepository(Organization::class);
            $ormRepository = $this->entityManager->getRepository(CodeRepo::class);

            $organization = $ormOrgRepository->findOneByName($organizationName);

            if (is_null($organization))
            {
                $organization = new Organization();
                $organization->setName($organizationName);
            }

            $codeRepo = $ormRepository->findOneByName($repository->name);

            if (is_null($codeRepo)) {
                $codeRepo = new CodeRepo();
            }

            $codeRepo->setOrganization($organization);
            $codeRepo->setName($repository->name);
            $codeRepo->setTrustScore($repository->trustScore);
            $codeRepo->setCreationDate($repository->getCreationDate());
            $this->entityManager->persist($codeRepo);
        }

        $result = $this->entityManager->flush();

        if ($result) {

            $io->success('Import successful!');
            return Command::SUCCESS;

        } else {
            $io->error('Import failed!');
            return Command::FAILURE;
        }

    }
}
