<?php

namespace App\Action\User;

use App\Action\Action;
use App\Action\ActionInterface;
use App\Domain\User\Service\UserCreationService;
use DI\Attribute\Inject;
use Nyholm\Psr7\Response;

final class RegisterUserAction extends Action implements ActionInterface
{
    #[Inject]
    private UserCreationService $userCreationService;

    public function action(): Response
    {
        if($user = $this->userCreationService->registerNewUser($this->request->getParsedBody())) {
            $this->addSuccessMessage("Your account request has been received and is awaiting approval");
            return $this->redirectFor('home');
        }
        return $this->render('home/home.html.twig');
    }
}
