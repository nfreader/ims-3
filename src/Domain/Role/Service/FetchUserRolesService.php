<?php

namespace App\Domain\Role\Service;

use App\Domain\Agency\Data\Agency;
use App\Domain\Agency\Service\FetchAgencyService;
use App\Domain\Role\Data\Role;
use App\Domain\Role\Repository\RoleRepository;
use App\Domain\User\Data\User;
use DI\Attribute\Inject;

class FetchUserRolesService
{
    #[Inject()]
    private FetchAgencyService $agencyService;

    #[Inject()]
    private RoleRepository $roleRepository;

    public function getRolesForUser(User $user)
    {
        return $this->roleRepository->getRolesForUser($user->getId());
    }

}
