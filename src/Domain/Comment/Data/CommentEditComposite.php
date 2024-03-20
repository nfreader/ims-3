<?php

namespace App\Domain\Comment\Data;

use App\Domain\Comment\Data\CommentEdit;
use App\Domain\Role\Data\RoleBadge;
use App\Domain\User\Data\UserBadge;
use DateTimeImmutable;

class CommentEditComposite
{
    public function __construct(
        private int $id,
        private int $comment,
        private string $previous,
        private string $current,
        private string $edited,
        //Editor user
        private int $editor,
        private string $editorName,
        private string $editorEmail,
        //Editor role
        private ?int $editorRoleId = null,
        private ?string $editorRoleName = null,
        private ?int $editorAgencyId = null,
        private ?string $editorAgencyName = null,
        private ?string $editorAgencyLogo = null
    ) {
    }

    public function getEdit(): CommentEdit
    {
        return new CommentEdit(
            id: $this->id,
            comment: $this->comment,
            previous: $this->previous,
            current: $this->current,
            edited: new DateTimeImmutable($this->edited),
            editor: new UserBadge(
                $this->editor,
                $this->editorName,
                $this->editorEmail
            ),
            editorRole: $this->editorRoleId ? new RoleBadge(
                $this->editorAgencyId,
                $this->editorAgencyName,
                $this->editorRoleId,
                $this->editorRoleName,
                $this->editorAgencyLogo,
                $this->editorName
            ) : null
        );
    }
}
