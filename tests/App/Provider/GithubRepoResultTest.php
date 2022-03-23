<?php

namespace App\Tests\App\Provider;

use PHPUnit\Framework\TestCase;
use App\Provider\GithubRepoResult;

class RepoResultTest extends TestCase
{
    public function testConstructorStoresNameFromJSON(): void
    {
        $json = json_encode([
            'name' => 'repository name',
        ]);

        $repoResult = new GithubRepoResult($json);
        $this->assertSame('repository name', $repoResult->name);
    }

    public function testUpdateStoresFieldValues(): void
    {
        $json = json_encode([
            'name' => 'repository name',
            'url' => 'repository url'
        ]);

        $repoResult = new GithubRepoResult($json);
        $this->assertNotSame('new url', $repoResult->url);
        $json = json_encode([
            'url' => 'new url'
        ]);

        $repoResult->update($json);
        $this->assertSame('new url', $repoResult->url);
    }
        
}
