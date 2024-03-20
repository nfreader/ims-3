<?php

namespace App\Domain\Comment\Data;

use App\Domain\Role\Data\RoleBadge;
use App\Domain\User\Data\UserBadge;
use DateTimeImmutable;

class CommentComposite
{
    public function __construct(
        private int $id,
        private string $text,
        private int $incident,
        private int $event,
        private string $created,
        private string $action,

        //Author
        private int $author,
        private string $authorName,
        private string $authorEmail,

        //Optional
        private ?string $updated = null,
        private array $edits = [],

        //Editor
        private ?string $editorName = null,
        private ?string $editorEmail = null,
        private ?int $editor = null,

        //Author role
        private ?int $authorRoleId = null,
        private ?string $authorRoleName = null,
        private ?int $authorAgencyId = null,
        private ?string $authorAgencyName = null,
        private ?string $authorAgencyLogo = null,

        //Editor role
        private ?int $editorRoleId = null,
        private ?string $editorRoleName = null,
        private ?int $editorAgencyId = null,
        private ?string $editorAgencyName = null,
        private ?string $editorAgencyLogo = null
    ) {

    }

    public function getComment(): Comment
    {
        return new Comment(
            id: $this->id,
            text: $this->text,
            author: new UserBadge(
                $this->author,
                $this->authorName,
                $this->authorEmail
            ),
            incident: $this->incident,
            event: $this->event,
            created: new DateTimeImmutable($this->created),
            action: CommentActionEnum::tryFrom($this->action),
            edited: $this->updated ? new DateTimeImmutable($this->updated) : null,
            editor: $this->updated ? new UserBadge(
                $this->editor,
                $this->editorName,
                $this->editorEmail
            ) : null,
            authorRole: $this->authorRoleId ? new RoleBadge(
                $this->authorAgencyId,
                $this->authorAgencyName,
                $this->authorRoleId,
                $this->authorRoleName,
                $this->authorAgencyLogo,
                $this->authorName
            ) : null,
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
