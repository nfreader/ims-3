<?php

namespace App\Domain\Permissions\Data;

enum PermissionTypeEnum: string
{
    case AGENCY = 'agency';
    case ROLE = 'role';
}
