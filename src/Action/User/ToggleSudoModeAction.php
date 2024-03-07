<?php

namespace App\Action\User;

use App\Action\Action;
use App\Action\ActionInterface;
use Nyholm\Psr7\Response;

class ToggleSudoModeAction extends Action implements ActionInterface
{
    public function action(): Response
    {
        if($this->getUser()->isAdmin()) {
            $this->getSession()->set('sudo_mode', !$this->getSession()->get('sudo_mode', false));
        }
        return $this->redirectFor('home');
    }
}
