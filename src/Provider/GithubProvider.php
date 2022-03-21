<?php

namespace App\Provider;

use App\Provider\Provider;
use GuzzleHttp\Client;

class GithubProvider extends Provider {

    const BASE_URL = 'https://api.github.com';

    private $client;

    public function __construct() {
        $this->client = new Client(['base_uri' => self::BASE_URL]);
    }

    public function import(string $organization):bool {
        $url = $this->getReposListURL($organization);
        $response = $this->client->get($url);
        $status = $response->getStatusCode();

        if ($status == 200) {
            $body = $response->getBody();
            $json = json_decode($body, true);
            foreach ($json as $item) {
                $name = $item['name'];
                $url = $item['url'];
                $response = $this->client->get($url);
                $status = $response->getStatusCode();
                $body = $response->getBody();
                $repoJson = json_decode($body, true);

                var_dump($repoJson);

            }
            return true;
        } else {
            return false;
        }
    }

    private function getReposListUrl(string $organization):string {
        return "orgs/{$organization}/repos?per_page=3";
    }


}
