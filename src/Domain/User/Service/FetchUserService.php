<?php

namespace App\Domain\User\Service;

use App\Domain\Agency\Repository\AgencyMembershipRepository;
use App\Domain\Role\Service\FetchUserRolesService;
use App\Domain\User\Data\User;
use App\Domain\User\Repository\UserRepository;
use DI\Attribute\Inject;

class FetchUserService
{
    #[Inject()]
    private UserRepository $userRepository;

    #[Inject()]
    private AgencyMembershipRepository $agencyMembership;

    #[Inject()]
    private FetchUserRolesService $roleService;

    public function getUser(int $id): User
    {
        $user = $this->userRepository->getUser($id);
        $user->setAgencies($this->agencyMembership->getAgenciesForUser($user->getId()));
        $user->setRoles($this->roleService->getRolesForUser($user));
        return $user;
    }

}
