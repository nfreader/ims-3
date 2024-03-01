<?php

namespace App\Domain\Role\Service;

use App\Domain\Agency\Data\Agency;
use App\Domain\Agency\Service\FetchAgencyService;
use App\Domain\Role\Data\Role;
use App\Domain\Role\Repository\RoleRepository;
use App\Domain\User\Data\User;
use DI\Attribute\Inject;

class FetchAgencyRolesService
{
    #[Inject()]
    private FetchAgencyService $agencyService;

    #[Inject()]
    private RoleRepository $roleRepository;

    public function getAgency(int $id): Agency
    {
        return $this->agencyService->getAgency($id);
    }

    public function getRolesForAgency(int $agency): array
    {
        return $this->roleRepository->getRolesForAgency($agency);
    }

    public function getRole(int $role): Role
    {
        return $this->roleRepository->getRole($role);
    }

    public function getUsersInRole(int $role): array
    {
        return $this->roleRepository->getUsersForRole($role);
    }

}
