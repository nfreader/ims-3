<?php

namespace App\Domain\User\Service;

use App\Domain\Role\Service\FetchUserRolesService;
use App\Domain\User\Data\User;
use App\Domain\User\Repository\UserRepository;
use DI\Attribute\Inject;
use Exception;

class FetchUserService
{
    #[Inject()]
    private UserRepository $userRepository;

    #[Inject()]
    private FetchUserRolesService $roleService;


    public function getUser(int $id): User
    {
        $user = $this->userRepository->getUser($id);
        $user->setRoles($this->roleService->getRolesForUser($user));
        return $user;
    }

    public function findUserByEmail(string $email): ?User
    {
        try {
            return $this->userRepository->getUserByEmail($email);
        } catch(Exception $e) {
            return false;
        }
    }

}
