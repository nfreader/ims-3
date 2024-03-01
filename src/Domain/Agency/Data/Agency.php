<?php

namespace App\Domain\Agency\Data;

use DateTimeImmutable;

class Agency
{
    public function __construct(
        private int $id,
        private string $name,
        private DateTimeImmutable $created,
        private bool $active,
        private ?string $logo = null,
        private ?string $fullname = null,
        private ?string $location = null,
        private ?string $title = null,
        private ?int $roleCount = 0,
        private ?int $userCount = 0
    ) {

    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCreated(): DateTimeImmutable
    {
        return $this->created;
    }

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function getFullname(): ?string
    {
        return $this->fullname;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function isActive(): bool
    {
        return $this->active;
    }
}
