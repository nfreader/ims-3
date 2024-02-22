<?php

namespace App\Domain\Event\Exception;

use DomainException;
use Throwable;

class EventNotFoundException extends DomainException
{
    public function __construct(
        string $message = "The requested event could not be located",
        int $code = 404,
        Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }

}
