<?php

namespace App\Domain\Attachment\Data;

use DateTimeImmutable;

class Attachment
{
    public function __construct(
        private int $id,
        private int $filename,
        private int $uploader,
        private DateTimeImmutable $uploaded,
        private int $incident,
        private ?int $event
    ) {

    }
}
