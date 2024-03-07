<?php

namespace App\Action\User;

use App\Action\Action;
use App\Action\ActionInterface;
use App\Domain\User\Service\UserCreationService;
use DI\Attribute\Inject;
use Nyholm\Psr7\Response;

final class CreateNewUserAction extends Action implements ActionInterface
{
    #[Inject]
    private UserCreationService $userCreationService;

    public function action(): Response
    {
        $data = $this->request->getParsedBody();
        $data['password'] = '';
        $user = $this->userCreationService->registerNewUser($data, true);
        $this->addSuccessMessage("User account created successfully");
        return $this->redirectFor('users.home');
    }
}
