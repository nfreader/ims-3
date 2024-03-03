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
    private RoleRepository $roleRepository;

    public function getRolesForUser(User $user): array
    {
        return $this->roleRepository->getRolesForUser($user->getId());
    }

}
