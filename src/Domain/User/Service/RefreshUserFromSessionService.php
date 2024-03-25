<?php

namespace App\Domain\User\Service;

use App\Domain\Profile\Repository\ProfileRepository;
use App\Domain\Profile\Service\FetchUserSettingsService;
use App\Domain\Role\Repository\RoleRepository;
use App\Domain\User\Data\User;
use App\Domain\User\Repository\UserRepository;
use Doctrine\DBAL\Connection;
use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\Session\Session;

class RefreshUserFromSessionService
{
    private UserRepository $userRepository;

    private RoleRepository $roleRepository;

    private ProfileRepository $profileRepository;

    public function __construct(private ContainerInterface $container)
    {
        $this->userRepository = new UserRepository($container->get(Connection::class));
        $this->roleRepository = new RoleRepository($container->get(Connection::class));
        $this->profileRepository = new ProfileRepository($container->get(Connection::class));
    }

    public function refreshUser(): ?User
    {
        $session = $this->container->get(Session::class);
        $user = $session->get('user', null);
        if($user) {
            $user = $this->userRepository->getUser($session->get('user'));
            $user->setRoles($this->roleRepository->getRolesForUser($user->getId()));
            foreach($user->getRoles() as $r) {
                if($r->getRoleId() === (int) $session->get('activeRole', null)) {
                    $user->setActiveRole($r);
                }
            }
            if($user->isAdmin()) {
                $user->setSudoMode($session->get('sudo_mode', false));
            }
            $settings = $this->profileRepository->getProfileForUser($user->getId());
            $user->setPreferences(FetchUserSettingsService::mapSettings($settings));
            return $user;
        }
        return null;
    }

}
