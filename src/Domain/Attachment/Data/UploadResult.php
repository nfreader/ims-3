<?php

namespace App\Domain\Attachment\Data;

class UploadResult
{
    public function __construct(
        public bool $error = true,
        public ?string $message = null,
        public ?string $file = null,
        public ?string $originalName = null,
        public ?string $hash = null,
        public ?int $id = null,
        public ?string $mimeType = null
    ) {
    }
}
