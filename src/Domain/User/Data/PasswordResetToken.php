<?php

namespace App\Domain\User\Data;

use App\Domain\User\Data\User;

/**
 * PasswordResetToken
 *
 * Handles setting up a new password reset token
 *
 */
class PasswordResetToken
{
    public const HASH_ALGO = 'sha512';

    public function __construct(
        /**
         * selector
         *
         * The cleartext "identifier" provided to the user so the reset token
         * can be retrieved from the database
         *
         * @var string
         */
        private string $selector,

        /**
         * validator
         *
         * Given to the user in cleartext, and a hashed version is stored in the
         * database.
         *
         * @var string
         */
        private string $validator,

        /**
         * user
         *
         * User instance acquired from the known email address
         *
         * @var User
         */
        private User $user
    ) {

    }

    public static function newToken(User $user): static
    {
        $rand = bin2hex(random_bytes(32));
        $selector = substr($rand, 0, 32);
        $validator = substr($rand, 32);
        return new self($selector, $validator, $user);
    }

    public function getSelector(): string
    {
        return $this->selector;
    }

    public function getValidator(): string
    {
        return $this->validator;
    }

    public function getHashedValidator(string $key): string
    {
        return bin2hex(hash_hmac(self::HASH_ALGO, $this->validator, $key, true));
    }

    public function getClearTextToken(): string
    {
        return $this->selector.$this->validator;
    }

    public function getUser(): User
    {
        return $this->user;
    }

}
