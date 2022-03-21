<?php

namespace App\Provider;

use App\Provider\IRepoResult;
use App\Provider\IProvider;
use GuzzleHttp\Client;

abstract class RepoResult implements IRepoResult {

    protected string $name;
    protected string $url;
    protected float $trustScore;

    protected IProvider $provider;
    protected Client $client;

    public function __construct(string $json, IProvider $provider) {
        $this->provider = $provider;
        $this->client = $provider->getClient();
        $this->update($json);
    }

    public function update(string $json): bool {
        $json = json_decode($json, true);

        foreach ($json as $key => $value) {
            $this->$key = $value;
        }

        return true;
    }

    public function __set(string $name, mixed $value) {
        if (property_exists($this, $name)) {
            $this->$name = $value;
        }
    }

    public function __get(string $name) {
        if (property_exists($this, $name)) {
            return $this->$name;
        } else {
            print "Attempting to read non-existent property {$name} in " . __CLASS__ . PHP_EOL;
            return null;
        }
    }
}
