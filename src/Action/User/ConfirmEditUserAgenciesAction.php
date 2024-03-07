<?php

namespace App\Action\User;

use App\Action\Action;
use App\Action\ActionInterface;
use App\Domain\Agency\Service\AgencyMembershipService;
use DI\Attribute\Inject;
use Nyholm\Psr7\Response;

class ConfirmEditUserAgenciesAction extends Action implements ActionInterface
{
    #[Inject()]
    private AgencyMembershipService $agencyMembership;

    public function action(): Response
    {
        $changes = $this->getSession()->get('agency_changes', null);
        if(!$changes) {
            $this->addMessage("No changes to make");
            return $this->redirectFor('users.home');
        }
        $this->agencyMembership->confirmAgencyChanges($changes, $this->getUser());
        $this->addSuccessMessage("This users agency memberships have been updated");
        $this->getSession()->set('agency_changes', null);
        return $this->redirectFor('user.view', ['user' => $changes[0]['target']]);
        // $this->getSession()->set('agency_changes', null);
        // return $this->render('manage/user/userAgencyCheck.html.twig', [
        //     'changes' => $changes
        // ]);
    }

}
