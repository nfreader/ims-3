<?php

namespace App\Domain\Role\Service;

use App\Domain\Agency\Exception\AgencyNotFoundException;
use App\Domain\Agency\Service\FetchAgencyService;
use App\Domain\Role\Repository\RoleRepository;
use DI\Attribute\Inject;
use Exception;

class CreateRoleService
{
    #[Inject()]
    private FetchAgencyService $agencyService;

    #[Inject()]
    private RoleRepository $roleRepository;

    public function createNewRole(int $agency, string $name)
    {
        $agency = $this->agencyService->getAgency($agency);
        return $this->roleRepository->insertNewRole($agency->getId(), $name);
    }

}
