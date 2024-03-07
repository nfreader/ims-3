<?php

namespace App\Data;

use App\Domain\Permissions\Data\PermissionsEnum;
use App\Domain\User\Data\User;

interface CheckPermissionsInterface
{
    /**
     * checkUserPermissions
     *
     * Checks if the user's current active role has the required permissions set
     *
     * @param PermissionsEnum $permission
     * @param User $user
     * @return boolean
     */
    public function checkUserPermissions(PermissionsEnum $permission, User $user): bool;
}
