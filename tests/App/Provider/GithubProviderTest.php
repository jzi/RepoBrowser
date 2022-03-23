<?php

namespace App\Tests\App\Provider;

use PHPUnit\Framework\TestCase;
use App\Provider\Provider;
use App\Provider\GithubProvider;

class GithubProviderTest extends TestCase
{
    public function testSomething(): void
    {
        $this->assertTrue(true);
    }

    /**
      * @dataProvider provideValidOrganizationNames
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
    

    public function provideValidOrganizationNames(): array
    {
        return [
            ['microsoft', 'orgs/microsoft/repos'],
            ['adobe', 'orgs/adobe/repos'],
            ['google', 'orgs/google/repos']
        ];
    }
}
