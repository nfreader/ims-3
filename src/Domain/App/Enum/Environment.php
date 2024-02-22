<?php

namespace App\Domain\App\Enum;

enum Environment: string
{
    case LOCAL = 'local';
    case DEV = 'dev';
    case TEST = 'test';
    case PROD = 'prod';

    public function enableDebug(): bool
    {
        return match($this) {
            Environment::DEV, Environment::LOCAL => true,
            default => false
        };
    }

}
