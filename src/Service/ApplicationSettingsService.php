<?php

namespace App\Service;

class ApplicationSettingsService
{
    public function __construct(
        private array $settings
    ) {

    }

    public function getSettings(): array
    {
        return $this->settings;
    }

}
