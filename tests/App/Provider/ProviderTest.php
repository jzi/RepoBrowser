<?php

namespace App\Tests\App\Provider;

use PHPUnit\Framework\TestCase;
use App\Provider\Provider;

class ProviderTest extends TestCase
{
    public function testiGetCreatesGithubProviderByName(): void
    {
        $providerName = 'Github';
        $provider = Provider::get($providerName);
        $this->assertInstanceOf(\App\Provider\GithubProvider::class, $provider);
    }

    public function testGetThrowsForNonExistentProvider(): void
    {
        $providerName = 'non-existent';
        $this->expectException(\Exception::class);
        $provider = Provider::get($providerName);
    }
}
