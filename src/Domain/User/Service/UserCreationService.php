<?php

namespace App\Domain\User\Service;

use App\Domain\User\Data\User;
use App\Domain\User\Repository\UserRepository;
use App\Exception\ValidationException;

class UserCreationService
{
    public function __construct(
        private UserRepository $userRepository
    ) {
    }

    public function registerNewUser(array $data, bool $skipValidation = false): User
    {
        if(!$skipValidation) {
            UserValidator::validateUser($data);
        }
        if ($this->userRepository->getUserByEmail($data['email'])) {
            throw new ValidationException('Errors were detected', ['email' => 'This username or email address is already in use']);
        }
        $this->userRepository->insertNewUser(
            $data['firstName'],
            $data['lastName'],
            $data['email'],
            password_hash($data['password'], PASSWORD_DEFAULT)
        );
        return $this->userRepository->getUserByEmail($data['email']);
    }
}
