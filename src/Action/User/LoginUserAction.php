<?php

namespace App\Action\User;

use App\Action\Action;
use App\Domain\User\Service\UserAuthenticationService;
use App\Domain\User\Service\UserCreationService;
use DI\Attribute\Inject;
use Nyholm\Psr7\Response;

final class LoginUserAction extends Action
{
    #[Inject]
    private UserAuthenticationService $userAuthenticationService;

    public function action(): Response
    {
        $this->userAuthenticationService->authenticateUser($this->request->getParsedBody());
        $this->addSuccessMessage("You have successfully logged into your account");
        return $this->redirectFor('home');
    }
}
