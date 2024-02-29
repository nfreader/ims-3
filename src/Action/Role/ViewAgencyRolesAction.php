<?php

namespace App\Action\Role;

use App\Action\Action;
use App\Domain\Role\Service\FetchAgencyRolesService;
use DI\Attribute\Inject;
use Nyholm\Psr7\Response;

class ViewAgencyRolesAction extends Action
{
    #[Inject()]
    private FetchAgencyRolesService $rolesService;

    public function action(): Response
    {
        $agency = $this->rolesService->getAgency($this->getArg('agency'));
        $roles = $this->rolesService->getRolesForAgency($agency->getId());
        return $this->render('manage/role/listing.html.twig', [
            'agency' => $agency,
            'roles' => $roles,
            'activetab' => 'roles'
        ]);
    }
}
