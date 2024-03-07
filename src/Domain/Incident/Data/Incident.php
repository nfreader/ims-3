<?php

namespace App\Domain\Incident\Data;

use App\Data\CheckPermissionsInterface;
use App\Domain\Permissions\Data\PermissionsEnum;
use App\Domain\Permissions\Data\PermissionTypeEnum;
use App\Domain\User\Data\User;
use DateTimeImmutable;
use JsonSerializable;

class Incident implements JsonSerializable, CheckPermissionsInterface
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
        private ?int $roleId = null,
        private array $permissions = []
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

    public function getPermissions(): array
    {
        return $this->permissions;
    }

    public function setPermissions(array $permissions): static
    {
        $this->permissions = array_fill_keys(array_column(PermissionTypeEnum::cases(), 'value'), []);
        foreach($permissions as $p) {
            $this->permissions[$p->getType()->value][] = $p;
        }
        // $this->permissions = $permissions;

        return $this;
    }

    public function checkUserPermissions(PermissionsEnum $permission, User $user): bool
    {
        // if($user->isAdmin()) {
        //     return true;
        // }
        if(!$this->getAgencyId() && $permission === PermissionsEnum::VIEW_INCIDENT) {
            //This is a "public" incident, which are always visible to all users
            return true;
        } elseif(!$user->getActiveRole()) {
            return false;
        } else {
            foreach($this->getPermissions()['role'] as $p) {
                if($permission->value & $p->getFlags()) {
                    if($permission->value & $user->getActiveRole()->getFlags()) {
                        return true;
                    }
                }
            }
        }
        return false;
    }
}
