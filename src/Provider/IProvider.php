<?php

namespace App\Provider;

use GuzzleHttp\Client;

interface IProvider {

    public function import(string $organization): bool;

    public function getClient(): Client;
}
