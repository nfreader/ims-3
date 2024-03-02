<?php

namespace App\Domain\Incident\Data;

use App\Domain\Agency\Data\Agency;
use DateTimeImmutable;
use JsonSerializable;

class Incident implements JsonSerializable
{
    public function __construct(
        private int $id,
        private string $name,
        private int $creator,
        private DateTimeImmutable $created,
        private string $creatorName,
        private string $creatorEmail,
        private ?string $agencyName = null,
        private ?int $agencyId = null,
        private ?string $agencyLogo = null,
        private ?string $roleName = null,
        private ?int $roleId = null
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getCreator(): int
    {
        return $this->creator;
    }

    public function getCreated(): DateTimeImmutable
    {
        return $this->created;
    }

    public function getCreatorName(): string
    {
        return $this->creatorName;
    }

    public function getCreatorEmail(): string
    {
        return $this->creatorEmail;
    }

    public function jsonSerialize(): mixed
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'created' => $this->getCreated(),
            'created_by' => [
                'name' => $this->getCreatorName(),
                'email' => $this->getCreatorEmail()
            ]
        ];
    }

    public function getAgencyId(): ?int
    {
        return $this->agencyId;
    }

    public function getAgencyLogo(): ?string
    {
        return $this->agencyLogo;
    }
}
