<?php

namespace App\Provider;

use App\Provider\Provider;
use GuzzleHttp\Client;
use App\Provider\GithubRepoResult;

class GithubProvider extends Provider implements IAuthable {
    
    use Authable;

    const BASE_URL = 'https://api.github.com';
    const REPOS_PER_PAGE = 3;

    public function import(string $organization, ?Client $client = null): array {

        if ($client !== null) {
            $this->client = $client;
        } else {
        
            $clientOptions = ['base_uri' => static::BASE_URL];
            if ($this->areCredentialsSet()) {
                $clientOptions['auth'] = array_values($this->getCredentials());
            }
            $this->client = new Client($clientOptions);
        }

        $repositories = $this->fetchRepositories($organization);
        $result = [];

        foreach ($repositories as $repository) {
            $url = $repository['url'];
            $repoResult = $this->fetchSingleRepository($url);
            $result[]= $repoResult;
        }

        return $result;
    }

    public function fetchRepositories(string $organization): array {

        $url = $this->getReposListURL($organization);
        $response = $this->client->get($url);
        $status = $response->getStatusCode();

        if ($status == 200) {
            $body = $response->getBody();
            $json = json_decode($body, true);

            return $json;
       
        } else {
            return [];
        }
    }

    public function fetchSingleRepository(string $url): GithubRepoResult {

        $response = $this->client->get($url);
        $body = $response->getBody();
        $repoResult = new GithubRepoResult($body, $this);

        $url = $this->getCollectionUrl($repoResult->commits_url);
        $response = $this->client->get($url);
        $body = $response->getBody();

        $commits = count(json_decode($body, true));
        $repoResult->commits_count = $commits;
        
        $url = $this->getCollectionUrl($repoResult->pulls_url);
        $response = $this->client->get($url);
        $body = $response->getBody();
        $pulls = count(json_decode($body, true));
        $repoResult->pull_requests_count = $pulls;

        $stargazers = $repoResult->stargazers_count;

        print "Found repository {$repoResult->name} with {$commits} commits, {$pulls} pull requests and {$stargazers} stargazers";
        $trustScore = $repoResult->calculateTrustScore();
        print " - trust score is {$trustScore}" . PHP_EOL;

        return $repoResult;
    }

    private function getReposListUrl(string $organization):string {
        return "orgs/{$organization}/repos?per_page=" . static::REPOS_PER_PAGE;
    }


    private function getCollectionUrl(string $url): string {
        $url = preg_replace('/{.*?}$/', '', $url);

        return $url;
    }
}
