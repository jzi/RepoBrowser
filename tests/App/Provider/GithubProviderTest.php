<?php

namespace App\Tests\App\Provider;

use PHPUnit\Framework\TestCase;
use App\Provider\Provider;
use App\Provider\GithubProvider;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Exception\RequestException;

class GithubProviderTest extends TestCase
{

    /**
      * @dataProvider provideValidOrganizations
      */
    public function testGithubProviderFetchesReposFromCorrectUrl(string $organization, string $url): void
    {
        $client = $this->createMock(\GuzzleHttp\Client::class);
        $client->expects($this->once())
            ->method('get')
            ->with($this->stringContains($url));

        $provider = Provider::get('Github');
        $provider->import($organization, $client);
    }

    public function testGithubProviderIsAuthable(): void
    {
        $provider = Provider::get('Github');
        $this->assertFalse($provider->areCredentialsSet());
        $provider->setCredentials('username', 'password');
        $this->assertTrue($provider->areCredentialsSet());
        $this->assertSame(['username' => 'username', 'password' => 'password'], $provider->getCredentials());
    }

    public function testGithubProviderThrowsWhenReadingUnsetCredentials(): void
    {
        $provider = Provider::get('Github');
        $this->expectException(\Exception::class);
        $provider->getCredentials();
    }


    public function testImportParsesResponseJson(): void
    {
        $responseBody = file_get_contents(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'response-github-microsoft.json');
        $repo1Body = file_get_contents(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'response-github-microsoft-repo1.json');
        $repo2Body = file_get_contents(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'response-github-microsoft-repo2.json');
        $repo3Body = file_get_contents(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'response-github-microsoft-repo3.json');

        $mock = new MockHandler([
            new Response(200, [], $responseBody),
            new Response(200, [], $repo1Body),
            new Response(200, [], '{}'),
            new Response(200, [], '{}'),
            new Response(200, [], $repo2Body),
            new Response(200, [], '{}'),
            new Response(200, [], '{}'),
            new Response(200, [], $repo3Body),
            new Response(200, [], '{}'),
            new Response(200, [], '{}'),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $reposUrl = 'orgs/microsoft/repos?per_page=3';
        $provider = Provider::get('github');
        $result = $provider->import('microsoft', $client);

        $this->assertCount(3, $result);

    }
    
    public function provideValidOrganizations(): array
    {
        return [
            ['microsoft', 'orgs/microsoft/repos'],
            ['adobe', 'orgs/adobe/repos'],
            ['google', 'orgs/google/repos']
        ];
    }
}
