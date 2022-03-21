<?php

namespace App\Provider;

interface IRepoResult {

    public function calculateTrustScore(): float;

    static public function fromJSON(string $json): IRepoResult;

}
