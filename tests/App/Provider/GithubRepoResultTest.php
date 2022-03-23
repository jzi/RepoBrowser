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
        
    /**
     * @dataProvider provideTrustScoreInputs
     */
    public function testTrustScoreIsCalculatedCorrectly(int $commits_count, int $pull_requests_count, int $stargazers_count, float $trustScore): void
    {
        $json = json_encode([
            'commits_count' => $commits_count,
            'pull_requests_count' => $pull_requests_count,
            'stargazers_count' => $stargazers_count,
        ]);

        $repoResult = new GithubRepoResult($json);
        $result = $repoResult->calculateTrustScore();

        $this->assertSame($trustScore, $result);
    }

    public function provideTrustScoreInputs(): array
    {
        return [
            [0, 0, 0, 0.0],
            [1, 2, 3, 9.4],
            [5, 10, 15, 47.0],

        ];
    }

    public function testMagicSetterUpdatesDefinedFields(): void
    {
        $repoResult = new GithubRepoResult('{}');
        $repoResult->name = 'repo name';
        $this->assertSame('repo name', $repoResult->name);
    }

    public function testMagicSetterSilentlyFailsForUndefinedFields(): void
    {
        $repoResult = new GithubRepoResult('{}');
        $repoResult->nonexistent_field = 1;
        $this->assertNull($repoResult->nonexistent_field);
    }
}
