<?php

namespace App\Provider;

interface IProvider {

    public function import(string $organization): bool;
}
