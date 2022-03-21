<?php

namespace App\Provider;

use App\Provider\RepoResult;

class GithubRepoResult extends RepoResult {

    protected string $starred_url;
    protected string $commits_url;
    protected string $pulls_url;
    protected array $organization;
    protected array $owner;

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
        return 0;
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
