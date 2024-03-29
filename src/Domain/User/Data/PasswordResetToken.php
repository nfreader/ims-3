<?php

namespace App\Domain\User\Data;

use App\Domain\User\Data\User;

class PasswordResetToken
{
    public const HASH_ALGO = 'sha512';

    public function __construct(
        private string $selector,
        private string $validator,
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
