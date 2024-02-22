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
        $this->getSession()->set('agency_changes', null);
        $changes = $this->agencyMembership->getAgencyMembershipChanges(
            $this->getArg('user'),
            $this->request->getParsedBody(),
        );
        $tmp = [];
        foreach($changes as $c) {
            $tmp[] = [
                'target' => $c['target']->getId(),
                'agency' => $c['agency']->getId(),
                'title' => $c['title'],
                'action' => $c['action']
            ];
        }
        $this->getSession()->set('agency_changes', $tmp);
        return $this->render('manage/user/userAgencyCheck.html.twig', [
            'changes' => $changes
        ]);
    }

}
