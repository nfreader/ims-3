<?php

namespace App\Domain\Role\Data;

class RoleBadge
{
    public function __construct(
        public int $agencyId,
        public string $agencyName,
        public int $roleId,
        public string $roleName,
        public ?string $logo,
        public ?string $creatorName
    ) {

    }

    public function getAgencyId(): int
    {
        return $this->agencyId;
    }

    public function getAgencyName(): string
    {
        return $this->agencyName;
    }

    public function getRoleId(): int
    {
        return $this->roleId;
    }

    public function getRoleName(): string
    {
        return $this->roleName;
    }

    public function getAgencyLogo(): ?string
    {
        return $this->logo;
    }

    public function getCreatorName(): ?string
    {
        return $this->creatorName;
    }
}
