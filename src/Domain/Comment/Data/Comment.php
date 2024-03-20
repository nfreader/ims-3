<?php

namespace App\Domain\Comment\Data;

use DateTimeImmutable;
use App\Domain\Comment\Data\CommentActionEnum;
use App\Domain\Role\Data\RoleBadge;
use App\Domain\User\Data\UserBadge;

class Comment
{
    public function __construct(
        private int $id,
        private string $text,
        private UserBadge $author,
        private int $incident,
        private int $event,
        private DateTimeImmutable $created,
        private CommentActionEnum $action,
        private ?DateTimeImmutable $edited = null,
        private ?UserBadge $editor = null,
        private array $edits = [],
        private ?RoleBadge $authorRole = null,
        private ?RoleBadge $editorRole = null
    ) {

    }

    public function getText(): string
    {
        return $this->text;
    }

    public function setText(string $text): static
    {
        $this->text = $text;

        return $this;
    }

    public function getCreated(): DateTimeImmutable
    {
        return $this->created;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getIncident(): int
    {
        return $this->incident;
    }

    public function getEvent(): int
    {
        return $this->event;
    }

    public function getAction(): CommentActionEnum
    {
        return $this->action;
    }

    public function getEdits(): array
    {
        return $this->edits;
    }

    public function setEdits(array $edits): self
    {
        $this->edits = array_reverse($edits);

        return $this;
    }

    public function getAuthor(): UserBadge
    {
        return $this->author;
    }

    public function getEditor(): ?UserBadge
    {
        return $this->editor;
    }

    public function getEdited(): ?DateTimeImmutable
    {
        return $this->edited;
    }


    public function getAuthorRole(): ?RoleBadge
    {
        return $this->authorRole;
    }

    public function getEditorRole(): ?RoleBadge
    {
        return $this->editorRole;
    }
}
