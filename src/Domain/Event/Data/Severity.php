<?php

namespace App\Domain\Event\Data;

enum Severity: string
{
    case INFORMATIONAL = 'informational';
    case LOW = 'low';
    case MEDIUM = 'medium';
    case HIGH = 'high';
    case CRITICAL = 'critical';

    public function getClass(): string
    {
        return match($this) {
            Severity::INFORMATIONAL => 'secondary',
            Severity::LOW => 'success',
            Severity::MEDIUM => 'warning',
            Severity::HIGH => 'danger',
            Severity::CRITICAL => 'critical'
        };
    }

    public function getShort(): string
    {
        return match ($this) {
            Severity::INFORMATIONAL => 'Info',
            Severity::LOW => 'Low',
            Severity::MEDIUM => 'Medium',
            Severity::HIGH => 'High',
            Severity::CRITICAL => 'Critical'
        };
    }

}
