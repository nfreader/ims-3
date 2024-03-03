<?php

namespace App\Domain\Permissions\Data;

use App\Domain\Permissions\Data\PermissionTypeEnum;

class Permissions
{
    public function __construct(
        private PermissionTypeEnum $type,
        private int $target,
        private int $incident,
        private int $flags
    ) {

    }

    public function getType(): PermissionTypeEnum
    {
        return $this->type;
    }

    public function getFlags(): int
    {
        return $this->flags;
    }

    public function getTarget(): int
    {
        return $this->target;
    }
}
