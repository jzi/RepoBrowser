<?php

namespace App\Provider;

interface IRepoResult {

    public function calculateTrustScore(): float;
    public function getCreationDate(): \DateTimeInterface;

}
