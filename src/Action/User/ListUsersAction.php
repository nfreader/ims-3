<?php

namespace App\Action\User;

use App\Action\Action;
use App\Domain\User\Repository\UserRepository;
use DI\Attribute\Inject;
use Nyholm\Psr7\Response;

class ListUsersAction extends Action
{
    #[Inject()]
    private UserRepository $userRepository;

    public function action(): Response
    {
        $users = $this->userRepository->getAllUsers();
        return $this->render('manage/user/users.html.twig', [
            'users' => $users
        ]);
    }

}
