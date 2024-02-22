<?php

namespace App\Exception;

use Exception;
use Throwable;

class RedirectWithMessageException extends Exception
{
    public function __construct(
        string $message,
        private string $route,
        private array $args = [],
        Throwable $previous = null
    ) {
        parent::__construct($message, 302, $previous);
    }

    public function getRoute(): string
    {
        return $this->route;
    }

    public function getArgs(): array
    {
        return $this->args;
    }

}
