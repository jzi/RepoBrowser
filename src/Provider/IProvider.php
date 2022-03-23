<?php

namespace App\Provider;

use GuzzleHttp\Client;

interface IProvider {

    public function import(string $organization): array;
    public function fetchRepositories(string $organization): array;
    public function fetchSingleRepository(string $repository): RepoResult;
}
