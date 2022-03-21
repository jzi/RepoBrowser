<?php

namespace App\Provider;

use App\Provider\Provider;
use GuzzleHttp\Client;
use App\Provider\GithubRepoResult;

class GithubProvider extends Provider implements IAuthable {
    
    use Authable;

    const BASE_URL = 'https://api.github.com';
    const REPOS_PER_PAGE = 3;

    public function import(string $organization):bool {

        $auth = array_values($this->getCredentials());
        $this->client = new Client(['base_uri' => static::BASE_URL, 'auth' => $auth]);

        $url = $this->getReposListURL($organization);
        $response = $this->client->get($url);
        $status = $response->getStatusCode();

        if ($status == 200) {
            $body = $response->getBody();
            $json = json_decode($body, true);

            foreach ($json as $item) {
                $url = $item['url'];
                $response = $this->client->get($url);
                $status = $response->getStatusCode();
                $body = $response->getBody();
                $repoResult = new GithubRepoResult($body, $this);

                $url = $this->getCollectionUrl($repoResult->commits_url);
                $response = $this->client->get($url);
                $body = $response->getBody();

                $commits = count(json_decode($body, true));
                
                $url = $this->getCollectionUrl($repoResult->pulls_url);
                $response = $this->client->get($url);
                $body = $response->getBody();

                $pulls = count(json_decode($body, true));

                $url = $this->getCollectionUrl($repoResult->owner['starred_url']);
                $response = $this->client->get($url);
                $body = $response->getBody();

                $stars = count(json_decode($body, true));

                print "Found repository {$repoResult->name} with {$commits} commits, {$pulls} pull requests and {$stars} stars" . PHP_EOL;
                
            }
            return true;
        } else {
            return false;
        }
    }

    private function getReposListUrl(string $organization):string {
        return "orgs/{$organization}/repos?per_page=" . static::REPOS_PER_PAGE;
    }


    public function getCollectionUrl(string $url): string {
        $url = preg_replace('/{.*?}$/', '', $url);

        return $url;
    }
}
