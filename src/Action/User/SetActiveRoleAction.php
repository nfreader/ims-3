<?php

namespace App\Action\User;

use App\Action\Action;
use App\Action\ActionInterface;
use Nyholm\Psr7\Response;

class SetActiveRoleAction extends Action implements ActionInterface
{
    public function action(): Response
    {
        $data = $this->request->getParsedBody();
        if(-1 === $data['role']) {
            $this->getSession()->set('activeRole', null);
        } else {
            $this->getSession()->set('activeRole', $data['role']);
        }
        return $this->json('Okay');
    }

}
