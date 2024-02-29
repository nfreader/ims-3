<?php

namespace App\Domain\Role\Service;

use App\Domain\Role\Data\Role;
use App\Domain\Role\Repository\RoleRepository;
use App\Domain\User\Service\FetchUserService;
use App\Service\FlashMessageService;
use DI\Attribute\Inject;
use Exception;

class UpdateRoleService
{
    #[Inject()]
    private FetchAgencyRolesService $rolesService;

    #[Inject()]
    private RoleRepository $roleRepository;

    #[Inject()]
    private FlashMessageService $flash;

    #[Inject()]
    private FetchUserService $userService;

    public function updateRole(string $action, array $data)
    {
        $role = (int) $data['role'];
        $role = $this->rolesService->getRole($role);

        switch ($action) {
            default:
                break;

            case 'disable':
                $this->toggleRole($role);
                break;

            case 'removeUser':
                var_dump($data);
                break;
        }
    }

    private function toggleRole(Role $role)
    {
        $this->roleRepository->updateRole($role->getId(), [
            'active' => (int) !$role->isActive()
        ]);
        if($role->isActive()) {
            $this->flash->addSuccessMessage("{$role->getName()} has been disabled");
        } else {
            $this->flash->addSuccessMessage("{$role->getName()} has been enabled");
        }
    }

    public function updateUserRole(array $data)
    {
        $target = $this->userService->getUser($data['target']);
        $role = $this->rolesService->getRole($data['role']);
        if(!$role->isActive()) {
            throw new Exception("This role is disabled and cannot be modified", 401);
        }
        return $this->roleRepository->insertOrUpdateMembership($target->getId(), $role->getId());

    }

}
