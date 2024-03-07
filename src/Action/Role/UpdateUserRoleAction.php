<?php

namespace App\Action\Role;

use App\Action\Action;
use App\Action\ActionInterface;
use App\Domain\Role\Service\UpdateRoleService;
use DI\Attribute\Inject;
use Nyholm\Psr7\Response;

class UpdateUserRoleAction extends Action implements ActionInterface
{
    #[Inject()]
    private UpdateRoleService $rolesService;

    public function action(): Response
    {
        $result = $this->rolesService->updateUserRole($this->request->getParsedBody());
        return $this->json($result);
    }
}
