<?php

namespace App\Domain\User\Service;

use App\Domain\Agency\Repository\AgencyMembershipRepository;
use App\Domain\User\Data\User;
use App\Domain\User\Repository\UserRepository;
use DI\Attribute\Inject;

class FetchUserService
{
    #[Inject()]
    private UserRepository $userRepository;

    #[Inject()]
    private AgencyMembershipRepository $agencyMembership;

    public function getUser(int $id): User
    {
        $user = $this->userRepository->findOneBy([$id], 'u.id = ?');
        $user->setAgencies($this->agencyMembership->getAgenciesForUser($user->getId()));
        return $user;
    }

}
