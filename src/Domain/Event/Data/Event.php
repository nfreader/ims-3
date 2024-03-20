<?php

namespace App\Domain\Event\Data;

use DateTimeImmutable;
use App\Domain\Event\Data\Severity;
use App\Domain\Role\Data\RoleBadge;
use App\Domain\User\Data\UserBadge;

class Event
{
    private ?RoleBadge $roleBadge = null;

    public function __construct(
        private int $id,
        private string $title,
        private string $desc,
        private Severity $severity,
        private int $incident,
        private DateTimeImmutable $created,
        private UserBadge $creator,
        private ?RoleBadge $role = null,
        private ?DateTimeImmutable $edited = null,
        private ?UserBadge $editor = null,
        private ?int $comments = null
    ) {
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getSeverity(): Severity
    {
        return $this->severity;
    }

    public function getCreated(): DateTimeImmutable
    {
        return $this->created;
    }

    public function getDesc(): string
    {
        return $this->desc;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getCommentCount(): ?int
    {
        return $this->comments;
    }

    public function getEdited(): ?DateTimeImmutable
    {
        return $this->edited;
    }

    public function getCreatorBadge(): ?RoleBadge
    {
        return $this->role;
    }

    public function getCreator(): UserBadge
    {
        return $this->creator;
    }

    public function getEditor(): ?UserBadge
    {
        return $this->editor;
    }
}
