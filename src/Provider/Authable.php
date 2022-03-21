<?php

namespace App\Provider;

trait Authable {
    
    private string $__username;
    private string $__password;
    private bool $__credentialsSet = false;

    public function setCredentials(string $username, string $password) {
        $this->__username = $username;
        $this->__password = $password;
        $this->__credentialsSet = true;
    }

    public function getCredentials(): array {
        if ($this->__credentialsSet) {
            return ['username' => $this->__username, 'password' => $this->__password];
        } else {
            throw new \Exception("Trying to use credentials before they're set!");
        }
    }
}
