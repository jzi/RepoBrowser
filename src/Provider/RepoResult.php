<?php

namespace App\Provider;

use App\Provider\IRepoResult;

abstract class RepoResult implements IRepoResult {

    protected string $name;
    protected string $url;
    protected float $trustScore;
    protected string $created_at;

    public function __construct(string $json) {
        $this->update($json);
    }

    public function update(string $json): bool {
        $json = json_decode($json, true, 512, JSON_THROW_ON_ERROR);

        foreach ($json as $key => $value) {
            $this->$key = $value;
        }

        return true;
    }

    public function getCreationDate(): \DateTimeInterface {
        return new \DateTime($this->created_at);
    }

    public function __set(string $name, mixed $value) {
        if (property_exists($this, $name)) {
            $this->$name = $value;
        }
    }

    public function __get(string $name) {
        if (property_exists($this, $name)) {
            return $this->$name;
        } else {
            throw new \OutOfBoundsException("Property {$name} not defined for class " . __CLASS__);
        }
    }
}
