<?php

namespace App\Tests\App\Provider;

use PHPUnit\Framework\TestCase;
use App\Provider\Provider;
use App\Provider\GithubProvider;

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

    

    public function provideValidOrganizations(): array
    {
        return [
            ['microsoft', 'orgs/microsoft/repos'],
            ['adobe', 'orgs/adobe/repos'],
            ['google', 'orgs/google/repos']
        ];
    }
}
