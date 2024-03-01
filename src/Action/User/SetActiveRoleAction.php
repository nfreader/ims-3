<?php

namespace App\Action\User;

use App\Action\Action;
use Nyholm\Psr7\Response;

class SetActiveRoleAction extends Action
{
    public function action(): Response
    {
        $user = $this->getUser();
        $data = $this->request->getParsedBody();
        if(-1 === $data['role']) {
            $this->getSession()->set('activeRole', null);
        } else {
            $this->getSession()->set('activeRole', $data['role']);
        }
        return $this->json('Okay');
    }

}
