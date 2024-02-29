<?php

namespace App\Domain\Agency\Exception;

use DomainException;
use Throwable;

class AgencyNotFoundException extends DomainException
{
    public function __construct(
        string $message = "The specified agency could not be located",
        int $code = 404,
        Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }

}
