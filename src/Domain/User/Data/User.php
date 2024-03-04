<?php

namespace App\Domain\User\Data;

use App\Domain\Agency\Data\Agency;
use App\Domain\Incident\Data\Incident;
use App\Domain\Permissions\Data\PermissionsEnum;
use App\Domain\Role\Data\UserRole;
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
        private bool $isAdmin,
        private bool $status = false,
        private array $agencies = [],
        private array $roles = [],
        private ?Agency $activeAgency = null,
        private ?UserRole $activeRole = null,
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

    public function setActiveRole(?UserRole $activeRole): self
    {
        $this->activeRole = $activeRole;
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

    public function isUserInRole(int $role): bool
    {
        $list = $this->getRoles();
        if($list) {
            if(in_array($role, array_column($list, 'roleId'))) {
                return true;
            }
        }
        return false;
    }

    public function getAgencyTitle(): ?string
    {
        return $this->agencyTitle;
    }


    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function getActiveRole(): ?UserRole
    {
        return $this->activeRole;
    }

    public function isAdmin(): bool
    {
        return $this->isAdmin;
    }

    public function can(PermissionsEnum $permission, mixed $target): bool
    {
        switch(get_class($target)) {
            case Incident::class:
                return $this->checkPermissionsAgainstIncident(
                    $permission,
                    $target
                );
                break;
        }
        return false;
    }

    private function checkPermissionsAgainstIncident(PermissionsEnum $permission, Incident $incident): bool
    {
        if(!$incident->getAgencyId() && $permission === PermissionsEnum::VIEW_INCIDENT) {
            //This is a "public" incident, which are always visible to all users
            return true;
        } elseif(!$this->getActiveRole()) {
            return false;
        } else {
            foreach($incident->getPermissions()['role'] as $p) {
                if($permission->value & $p->getFlags()) {
                    if($permission->value & $this->getActiveRole()->getFlags()) {
                        return true;
                    }
                }
            }
        }
        return false;
    }
}
