<?php

namespace App\Provider;

interface IAuthable extends IProvider {
    public function getCredentials(): array;
    public function setCredentials(string $username, string $password);
}
