<?php

namespace App\Tests\App\Command;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use App\Entity\CodeRepo;

class RepoImportCommandTest extends KernelTestCase
{
    public function testExecute(): void
    {
        $kernel = self::bootKernel();
        $application = new Application($kernel);

        $command = $application->find('app:repo:import');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'organization' => 'microsoft',
            'provider'     => 'Github'
        ]);

        $commandTester->assertCommandIsSuccessful();

        // the output of the command in the console
        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('Import successful', $output);

        $em = $kernel->getContainer()->get('doctrine.orm.entity_manager');
        $repo = $em->getRepository(CodeRepo::class);

        $this->assertCount(3, $repo->findAll());
    }

    public function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
        $em = $this->getEntityManager();
        $purger = new ORMPurger($em);
        $purger->purge();
    }

    /**
     * @return EntityManager
     */
    private function getEntityManager()
    {
        return self::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }
}
