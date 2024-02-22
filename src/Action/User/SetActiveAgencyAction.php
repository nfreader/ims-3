<?php

namespace App\Action\User;

use App\Action\Action;
use Nyholm\Psr7\Response;

class SetActiveAgencyAction extends Action
{
    public function action(): Response
    {
        $user = $this->getUser();
        $data = $this->request->getParsedBody();
        if(-1 === $data['agency']) {
            $this->getSession()->set('activeAgency', null);
        } elseif($user->isUserInAgency($data['agency'])) {
            $this->getSession()->set('activeAgency', $data['agency']);
        } else {
            $this->getSession()->set('activeAgency', null);
        }
        return $this->json('Okay');
    }

}
