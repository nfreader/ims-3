<?php

namespace App\Domain\User\Service;

use App\Domain\Agency\Repository\AgencyMembershipRepository;
use App\Domain\Role\Repository\RoleRepository;
use App\Domain\User\Data\User;
use App\Domain\User\Repository\UserRepository;
use Doctrine\DBAL\Connection;
use Exception;
use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\Session\Session;

class RefreshUserFromSessionService
{
    private UserRepository $userRepository;

    private AgencyMembershipRepository $membershipRepository;

    private RoleRepository $roleRepository;

    public function __construct(private ContainerInterface $container)
    {
        $this->userRepository = new UserRepository($container->get(Connection::class));
        $this->membershipRepository = new AgencyMembershipRepository($container->get(Connection::class));
        $this->roleRepository = new RoleRepository($container->get(Connection::class));
    }

    public function refreshUser(): ?User
    {
        $session = $this->container->get(Session::class);
        $user = $session->get('user', null);
        if($user) {
            $user = $this->userRepository->getUser($session->get('user'));
            $agencies = $this->membershipRepository->getAgenciesForUser($user->getId());
            $user->setAgencies($agencies);
            $user->setRoles($this->roleRepository->getRolesForUser($user->getId()));
            foreach($user->getRoles() as $r) {
                if($r->getRoleId() == (int) $session->get('activeRole', null)) {
                    $user->setActiveRole($r);
                }
            }
            return $user;
        }
        return null;
    }

}
