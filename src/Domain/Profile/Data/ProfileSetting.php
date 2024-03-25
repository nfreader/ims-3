<?php

namespace App\Domain\Profile\Data;

class ProfileSetting
{
    public function __construct(
        private string $name,
        private string $value,
        private bool $autoload = true
    ) {

    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
