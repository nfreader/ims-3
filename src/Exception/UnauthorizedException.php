<?php

namespace App\Exception;

use RuntimeException;
use Throwable;

/**
 * UnauthorizedException
 * 
 * An extended exception. Supposed to be caught and transformed into HttpException
 */
class UnauthorizedException extends RuntimeException
{
    public function __construct(
        string $message,
        Throwable $previous = null
    ) {
        parent::__construct($message, 403, $previous);
    }

}
