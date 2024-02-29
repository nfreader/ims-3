<?php

namespace App\Action\Role;

use App\Action\Action;
use App\Domain\Role\Service\CreateRoleService;
use App\Domain\Role\Service\FetchAgencyRolesService;
use DI\Attribute\Inject;
use Nyholm\Psr7\Response;

class CreateRoleAction extends Action
{
    #[Inject()]
    private CreateRoleService $rolesService;

    public function action(): Response
    {
        $this->rolesService->createNewRole($this->getArg('agency'), $this->request->getParsedBody()['name']);
        return $this->redirectFor('roles.view', ['agency' => $this->getArg('agency')]);
    }
}
