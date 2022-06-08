<?php

namespace App\Tests\Behat;

use App\Entity\CodeRepo;
use App\Entity\Organization;
use Behat\Behat\Context\Context;
use Behat\Behat\Tester\Exception\PendingException;
use Behat\Gherkin\Node\TableNode;
use Coduo\PHPMatcher\PHPMatcher;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Imbo\BehatApiExtension\Context\ApiContext;
use Imbo\BehatApiExtension\Exception\AssertionFailedException;

/**
 * Defines application features from the specific context.
 */
class FeatureContext extends ApiContext
{
    private EntityManagerInterface $entityManager;
    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    /**
     * @Given the following repositories exist
     */
    public function theFollowingRepositoriesExist(TableNode $table)
    {

        foreach ($table as $item)
        {
            $organization = new Organization();
            $organization->setName($item['organization_name']);
            $codeRepo = new CodeRepo();
            $codeRepo->setName($item['name'])
                ->setCreationDate(new \DateTime($item['creation_date']))
                ->setTrustScore($item['trust_score'])
                ->setOrganization($organization);
            $this->entityManager->persist($codeRepo);
        }

        $this->entityManager->flush();
    }

    /**
     * @Then the response contains the repository :repository from organization :organization
     * @throws AssertionFailedException
     */
    public function theResponseContainsTheRepositoryFromOrganization($repository, $organization)
    {
        $json = json_decode($this->response->getBody(), true);
        $matcher = new PHPMatcher();


        foreach ($json as $item) {

            $pattern = [
                'id' => '@integer@',
                'name' => $repository,
                'organization' => ['name' => $organization],
                'creation_date' => '@datetime@',
                'trust_score' => '@number@'
            ];

            $match = $matcher->match($item, $pattern);

            if ($match) return true;
        }

        throw new AssertionFailedException("Repository {$repository} of organization {$organization} not found in response");
    }

    /**
     * @BeforeSuite
     *
     * @return void
     */
    public static function beforeSuite()
    {
        return self::prepareDatabase();
    }

    private function purgeDatabase()
    {
        $purger = new ORMPurger($this->entityManager);
        $purger->purge();
    }

    private static function prepareDatabase()
    {
        $commands = [
            "bin/console doctrine:database:drop -n --if-exists --env=test --force",
            "bin/console doctrine:database:create -n --if-not-exists --env=test",
            "bin/console doctrine:migrations:migrate -n --env=test"
        ];

        foreach ($commands as $cmd) exec ($cmd);
    }

}
