<?php

namespace App\Domain\User\Data;

use App\Domain\Agency\Data\Agency;
use DateTimeImmutable;
use JsonSerializable;

class User implements JsonSerializable
{
    public function __construct(
        private int $id,
        private string $firstName,
        private string $lastName,
        private string $email,
        private string $password,
        private DateTimeImmutable $created,
        private int $createdIp,
        private bool $status = false,
        private array $agencies = [],
        private ?Agency $activeAgency = null,
        private ?string $agencyTitle = null,
        private ?string $agencyList = null
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function isStatus(): bool
    {
        return $this->status;
    }

    private function getPassword(): string
    {
        return $this->password;
    }

    public function checkPassword(string $providedPassword): bool
    {
        return password_verify($providedPassword, $this->getPassword());
    }

    public function getName(): string
    {
        return "{$this->firstName} {$this->lastName}";
    }

    public function jsonSerialize(): mixed
    {
        return [
            'id' => $this->getId(),
            'email' => $this->getEmail(),
            'status' => $this->isStatus(),
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
            'agencyList' => explode(',', $this->agencyList)
        ];
    }

    public function getCreated(): DateTimeImmutable
    {
        return $this->created;
    }

    public function setAgencies(array $agencies): static
    {
        $this->agencies = $agencies;
        return $this;
    }

    /**
     * getAgencyList
     *
     * Iterates over the array of agencies and returns a simple array of the agency ID and the user's title with that agency
     *
     * @return array|null
     */
    public function getAgencyList(): array
    {
        if (!$this->agencies) {
            return [];
        }
        return array_values(array_map(function ($a) {
            return ['id' => $a->getId(),'title' => $a->getTitle()];
        }, $this->agencies));
    }

    /**
     * getAgencies
     *
     * Returns a list of agencies the user is a member of. The array is empty if the user is not in any agencies.
     *
     * @return array
     */
    public function getAgencies(): array
    {
        return $this->agencies;
    }

    /**
     * getActiveAgency
     *
     * If the activeAgency property is set, iterates through the list of agencies the user is a member of and returns the one with the ID that matches the current active agency.
     *
     * @return ?Agency
     */
    public function getActiveAgency(): ?Agency
    {
        return $this->activeAgency;
    }

    /**
     * setActiveAgency
     *
     * Sets the $activeAgency property to the ID of the agency the user is currently accessing the application under. Returns false if no active agency is set.
     *
     * @param integer|null $activeAgency
     * @return self
     */
    public function setActiveAgency(?Agency $activeAgency): self
    {
        $this->activeAgency = $activeAgency;
        return $this;
    }

    public function isUserInAgency(int $agency): bool
    {
        $list = $this->getAgencyList();
        if($list) {
            if(in_array($agency, array_column($list, 'id'))) {
                return true;
            }
        }
        return false;
    }

    public function getAgencyTitle(): ?string
    {
        return $this->agencyTitle;
    }

}
