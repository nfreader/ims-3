<?php

namespace App\Exception;

use DomainException;
use Throwable;

class ValidationException extends DomainException
{
    private ?array $errors = null;

    public function __construct(
        string $message,
        array $errors = [],
        int $code = 422,
        Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->errors = $errors;
    }

    public function getErrors(): array|null
    {
        return $this->errors;
    }

}
