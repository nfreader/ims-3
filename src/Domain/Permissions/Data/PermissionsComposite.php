<?php

namespace App\Domain\Permissions\Data;

use App\Domain\Incident\Data\Incident;

class PermissionsComposite
{
    public function __construct(
        private string $type,
        private int $target,
        private int $incident,
        private int $flags
    ) {

    }

    public function getPermissions(): Permissions
    {
        return new Permissions(
            PermissionTypeEnum::tryFrom($this->type),
            $this->target,
            $this->incident,
            $this->flags
        );
    }

}
