<?php

namespace App\Action\Role;

use App\Action\Action;
use App\Domain\Role\Service\CreateRoleService;
use App\Domain\Role\Service\FetchAgencyRolesService;
use App\Domain\Role\Service\UpdateRoleService;
use DI\Attribute\Inject;
use Nyholm\Psr7\Response;

class UpdateRoleAction extends Action
{
    #[Inject()]
    private UpdateRoleService $rolesService;

    public function action(): Response
    {
        $this->rolesService->updateRole($this->getArg('action'), $this->request->getParsedBody());
        return $this->redirectFor('roles.view', ['agency' => $this->getArg('agency')]);
    }
}
