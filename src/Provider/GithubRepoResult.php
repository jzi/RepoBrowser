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

    static public function fromJSON(string $json): GithubRepoResult {

        $result = new GithubRepoResult();
        $json = json_decode($json, true);

        $result->name = $json['name'];
        $result->organization = $json['organization']['login'];
        $result->url = $json['url'];
        $result->commits_url = str_replace('{/sha}', '', $json['commits_url']);
        $result->pulls_url = str_replace('{/number}', '', $json['pulls_url']);

        return $result;
    }

    public function calculateTrustScore(): float {
        $result = 0.0;
        $result += $this->commits_count;
        $result += $this->pull_requests_count * 1.2;
        $result += $this->stargazers_count * 2;

        return $result;
    }

    public function getStarredUrl(): string {
        return $this->starredUrl;
    }

    public function getCommitsUrl(): string {
        return $this->commitsUrl;
    }
    
    public function getPullsUrl(): string {
        return $this->pullsUrl;
    }

}
