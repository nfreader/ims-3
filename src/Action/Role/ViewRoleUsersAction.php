<?php

namespace App\Action\Role;

use App\Action\Action;
use App\Action\ActionInterface;
use App\Domain\Role\Service\FetchAgencyRolesService;
use DI\Attribute\Inject;
use Nyholm\Psr7\Response;

class ViewRoleUsersAction extends Action implements ActionInterface
{
    #[Inject()]
    private FetchAgencyRolesService $rolesService;

    public function action(): Response
    {
        $agency = $this->rolesService->getAgency($this->getArg('agency'));
        $role = $this->rolesService->getRole($this->getArg('role'));
        $users = $this->rolesService->getUsersInRole($role->getId());
        return $this->render('manage/role/users.html.twig', [
            'agency' => $agency,
            'role' => $role,
            'activetab' => 'roles',
            'users' => $users
        ]);
    }
}
