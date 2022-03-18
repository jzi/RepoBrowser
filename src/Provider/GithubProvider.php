<?php

namespace App\Provider;

use App\Provider\Provider;

class GithubProvider extends Provider {

    public function import(string $organization):bool {
        return false;
    }
}
