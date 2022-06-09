<?php

namespace App\Tests\Behat;

use App\Entity\CodeRepo;
use App\Entity\Organization;
use Behat\Behat\Context\Context;
use Behat\Behat\Tester\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Coduo\PHPMatcher\PHPMatcher;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\RequestOptions;
use Imbo\BehatApiExtension\Context\ApiContext;
use Imbo\BehatApiExtension\Exception\AssertionFailedException;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Defines application features from the specific context.
 */
class FeatureContext extends ApiContext
{
    private EntityManagerInterface $entityManager;
    private KernelInterface $kernel;
    private Application $application;
    private BufferedOutput $output;
    private string $email;
    private string $password;
    private string $jwt;

    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        KernelInterface $kernel
    )
    {
        $this->entityManager = $entityManager;
        $this->kernel = $kernel;
        $this->application = new Application($kernel);
        $this->output = new BufferedOutput();
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
     * @Given a user exists with email :email and password :password
     * @throws AssertionFailedException
     */
    public function aUserExistsWithEmailAndPassword($email, $password)
    {
        $cmd = 'app:users:create';
        $input = new ArgvInput(['behat', $cmd, $email, $password]);

        $this->output->fetch();

        try {
            $this->application->doRun($input, $this->output);
            $this->email = $email;
            $this->password = $password;
        } catch (\Throwable $e) {
            throw new AssertionFailedException("Command {$cmd} failed!" . PHP_EOL . $e->getMessage());
        }

    }


    /**
     * @Given I am authenticated
     */
    public function iAmAuthenticated()
    {
        $reqBody = json_encode([
            'username' => $this->email,
            'password' => $this->password
        ]);

        $this->addRequestHeader('Content-Type', 'application/json');
        $this->setRequestBody($reqBody);
        $this->requestPath('/api/login_check', 'POST');
        $response = $this->getResponseBody();
        $this->jwt = $response->token;
        $this->addRequestHeader("Authorization", "Bearer {$this->jwt}");
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

    /**
     * @BeforeScenario
     *
     * @return void
     */
    public function beforeScenario()
    {
        $this->purgeDatabase();
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
