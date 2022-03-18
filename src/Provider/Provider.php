<?php

namespace App\Provider;

use App\Provider\IProvider;

abstract class Provider implements IProvider {
    
    static function get($name): IProvider {
        $className = "App\\Provider\\${name}Provider";
        
        if (class_exists($className)) {
            $provider = new $className();
        } else {
            throw new \Exception("Provider not found for ${name}!");
        } 
        return $provider;
    }
}
