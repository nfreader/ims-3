<?php

namespace App\Data;

use App\Domain\Permissions\Data\PermissionsEnum;
use App\Domain\User\Data\User;

interface CheckPermissionsInterface
{
    public function checkUserPermissions(PermissionsEnum $permission, User $user): bool;
}
