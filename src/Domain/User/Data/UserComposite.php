<?php

namespace App\Domain\User\Data;

use DateTimeImmutable;

class UserComposite
{
    public function __construct(
        private int $id,
        private string $firstName,
        private string $lastName,
        private string $email,
        private string $password,
        private string $created,
        private int $createdIp,
        private bool $isAdmin = false,
        private bool $status = false
    ) {
    }
    public function getUser(): User
    {
        return new User(
            id: $this->id,
            firstName: $this->firstName,
            lastName: $this->lastName,
            email: $this->email,
            password: $this->password,
            created: new DateTimeImmutable($this->created),
            createdIp: $this->createdIp,
            isAdmin: $this->isAdmin,
            status: $this->status,
        );
    }
}
