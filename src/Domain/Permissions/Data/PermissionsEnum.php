<?php

namespace App\Domain\Permissions\Data;

enum PermissionsEnum: int
{
    case VIEW_INCIDENT = (1 << 0);
    case EDIT_INCIDENT = (1 << 1);
    case POST_UPDATES  = (1 << 2);
    case EDIT_UPDATES  = (1 << 3);
    case ACTIVITY_LOG  = (1 << 4);
    case UPDATE_ROLES  = (1 << 5);

    public function getName(): string
    {
        return match ($this) {
            PermissionsEnum::VIEW_INCIDENT => 'View Incident',
            PermissionsEnum::EDIT_INCIDENT => 'Edit Incident',
            PermissionsEnum::POST_UPDATES => 'Post Updates',
            PermissionsEnum::EDIT_UPDATES => 'Edit Updates',
            PermissionsEnum::ACTIVITY_LOG => 'Use Activity Log',
            PermissionsEnum::UPDATE_ROLES => 'Update Roles',
        };
    }

    public static function fromName(string $name)
    {
        return constant("self::$name");
    }

}
