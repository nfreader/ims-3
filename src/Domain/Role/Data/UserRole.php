<?php

namespace App\Domain\Role\Data;

class UserRole
{
    public function __construct(
        private int $roleId,
        private string $roleName,
        private int $agencyId,
        private string $agencyName,
        private ?string $agencyLogo = null
    ) {

    }

    public function getRoleId(): int
    {
        return $this->roleId;
    }

    public function getRoleName(): string
    {
        return $this->roleName;
    }

    public function getAgencyId(): int
    {
        return $this->agencyId;
    }

    public function getAgencyName(): string
    {
        return $this->agencyName;
    }

    public function getAgencyLogo(): ?string
    {
        return $this->agencyLogo;
    }
}
