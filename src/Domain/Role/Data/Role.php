<?php

namespace App\Domain\Role\Data;

use DateTime;

class Role
{
    public function __construct(
        private int $id,
        private int $agency,
        private string $name,
        private DateTime $created,
        private bool $active,
        private int $users = 0
    ) {

    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getAgency(): int
    {
        return $this->agency;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCreated(): DateTime
    {
        return $this->created;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function getUsers(): int
    {
        return $this->users;
    }
}
