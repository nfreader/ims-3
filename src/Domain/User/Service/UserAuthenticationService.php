<?php

namespace App\Domain\User\Service;

use App\Domain\User\Data\User;
use App\Domain\User\Repository\UserRepository;
use App\Exception\ValidationException;
use Exception;
use Symfony\Component\HttpFoundation\Session\Session;

class UserAuthenticationService
{
    public function __construct(
        private UserRepository $userRepository,
        private Session $session
    ) {
    }

    public function authenticateUser(array $data): User
    {
        if (!$user = $this->userRepository->getUserByEmail($data['email'])) {
            throw new ValidationException('Errors were detected', ['This username or email address is already in use']);
        }
        if(!$user->checkPassword($data['password'])) {
            throw new Exception("Invalid password", 403);
        }
        $this->session->set('user', $user->getId());
        return $user;
    }
}
