<?php

namespace App\Domain\User\Data;

use App\Domain\Incident\Data\Incident;
use App\Domain\Permissions\Data\PermissionsEnum;
use App\Domain\Role\Data\UserRole;
use DateTimeImmutable;
use Exception;
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
        private ?UserRole $activeRole = null,
        private bool $sudoMode = false,
        private array $preferences = []
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

    public function setActiveRole(?UserRole $activeRole): static
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

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function setRoles(array $roles): static
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
    /**
     * can
     *
     * Based on the permissions and target, return whether or not this user can perform the requested action
     *
     * @param string|PermissionsEnum $permission
     * @param mixed $target
     * @return boolean
     */
    public function can(string|PermissionsEnum $permission, mixed $target): bool
    {
        if(is_string($permission)) {
            $permission = PermissionsEnum::fromName($permission);
        }
        switch(get_class($target)) {
            case Incident::class:
                return $target->checkUserPermissions(
                    $permission,
                    $this
                );
                break;
        }
        return false;
    }


    public function isSudoMode(): bool
    {
        return $this->sudoMode;
    }

    public function setSudoMode(bool $sudoMode): self
    {
        $this->sudoMode = $sudoMode;

        return $this;
    }

    /**
     * setPreferences
     * Sets the users preferences
     *
     * @param array $preferences<ProfileSetting>
     * @return static
     */
    public function setPreferences(array $preferences): static
    {
        $this->preferences = $preferences;
        return $this;
    }

    /**
     * getPreference
     * Get a single preference from the preferences array. If the requested
     * preference is not found, throws an exception
     *
     * @param string $preference
     * @return mixed
     * @throws Exception
     */
    public function getPreference(string $preference): mixed
    {
        $setting = $this->preferences[$preference] ?? null;
        if(!$setting) {
            throw new Exception("Invalid preference requested", 500);
        }
        return $setting;
    }
}
