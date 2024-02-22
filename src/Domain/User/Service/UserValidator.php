<?php

namespace App\Domain\User\Service;

use App\Exception\ValidationException;
use Cake\Validation\Validator;

final class UserValidator
{
    public static function validateUser(array $user): void
    {
        $validator = new Validator();
        $validator
            ->requirePresence('firstName', true, 'A first name is required')
            ->notEmptyString('lastName', 'A last name is required')
            ->requirePresence('email', true, 'An email address is required')
            ->email('email', false, 'An email address is required')
            ->requirePresence('password', true, 'You must specify a password')
            ->minLength('password', 10, 'Your password must be at least 10 characters long');

        $errors = $validator->validate($user);

        if($errors) {
            throw new ValidationException("Errors were detected", $errors);
        }
    }

}
