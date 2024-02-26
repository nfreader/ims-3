<?php

namespace App\Action\User;

use App\Action\Action;
use App\Domain\Agency\Service\AgencyMembershipService;
use DI\Attribute\Inject;
use Nyholm\Psr7\Response;

class EditUserAgenciesAction extends Action
{
    #[Inject()]
    private AgencyMembershipService $agencyMembership;

    public function action(): Response
    {
        $data = $this->request->getParsedBody();
        $this->agencyMembership->changeMembership($this->getArg('user'), $data['agency'], $this->getUser());

        return $this->json($this->request->getParsedBody());
        // return $this->render('manage/user/userAgencyCheck.html.twig', [
        //     'changes' => $changes
        // ]);
    }

}
