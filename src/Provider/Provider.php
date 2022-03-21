<?php

namespace App\Provider;

use App\Provider\IProvider;
use GuzzleHttp\Client;

abstract class Provider implements IProvider {
    
    protected Client $client;

    static function get($name): IProvider {
        $className = "App\\Provider\\${name}Provider";
        
        if (class_exists($className)) {
            $provider = new $className();
        } else {
            throw new \Exception("Provider not found for ${name}!");
        } 
        return $provider;
    }

    public function __construct() {
        $this->client = new Client(['base_uri' => static::BASE_URL]);
    }

    public function getClient(): Client {
        return $this->client;
    }
}
