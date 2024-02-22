<?php

namespace App\Domain\Event\Data;

use DateTimeImmutable;
use App\Domain\Event\Data\Severity;

class Event
{
    public function __construct(
        private int $id,
        private string $title,
        private string $desc,
        private Severity $severity,
        private int $incident,
        private int $creator,
        private DateTimeImmutable $created,
        private string $creatorName,
        private string $creatorEmail,
        private ?DateTimeImmutable $edited = null,
        private ?string $editorName = null,
        private ?string $editorEmail = null,
        private ?int $comments = null,
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

    public function getCreatorName(): string
    {
        return $this->creatorName;
    }

    public function getCreated(): DateTimeImmutable
    {
        return $this->created;
    }

    public function getDesc(): string
    {
        return $this->desc;
    }

    public function getCreatorEmail(): string
    {
        return $this->creatorEmail;
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

    public function getEditorName(): ?string
    {
        return $this->editorName;
    }
}
