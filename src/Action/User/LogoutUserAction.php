<?php

namespace App\Action\User;

use App\Action\Action;
use App\Action\ActionInterface;
use App\Domain\User\Service\UserAuthenticationService;
use App\Domain\User\Service\UserCreationService;
use DI\Attribute\Inject;
use Nyholm\Psr7\Response;

final class LogoutUserAction extends Action implements ActionInterface
{
    public function action(): Response
    {
        $session = $this->getSession();
        $session->invalidate();
        $this->addSuccessMessage("You have successfully logged out");
        return $this->redirectFor('home');
    }
}
