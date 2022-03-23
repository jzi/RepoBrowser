<?php

namespace App\Provider;

use App\Provider\RepoResult;

class GithubRepoResult extends RepoResult {

    protected string $starred_url;
    protected string $commits_url;
    protected string $pulls_url;
    protected array $organization;
    protected array $owner;
    protected int $stargazers_count;
    protected int $commits_count;
    protected int $pull_requests_count;

    public function calculateTrustScore(): float {
        $result = 0.0;
        $result += $this->commits_count;
        $result += $this->pull_requests_count * 1.2;
        $result += $this->stargazers_count * 2;

        return $result;
    }

}
