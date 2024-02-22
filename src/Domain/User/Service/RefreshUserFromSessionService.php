<?php

namespace App\Domain\User\Service;

use App\Domain\Agency\Repository\AgencyMembershipRepository;
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

    public function __construct(private ContainerInterface $container)
    {
        $this->userRepository = new UserRepository($container->get(Connection::class));
        $this->membershipRepository = new AgencyMembershipRepository($container);
    }

    public function refreshUser(): ?User
    {
        $session = $this->container->get(Session::class);
        $user = $session->get('user', null);
        if($user) {
            $user = $this->userRepository->getUser($session->get('user'));
            $user->setAgencies($this->membershipRepository->getAgenciesForUser($user->getId()));
            // $user->setActiveAgency($session->get('activeAgency', null));
            return $user;
        }
        return null;
    }

}
