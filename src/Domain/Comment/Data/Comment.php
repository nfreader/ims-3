<?php

namespace App\Domain\Comment\Data;

use DateTimeImmutable;
use App\Domain\Comment\Data\CommentActionEnum;

class Comment
{
    public function __construct(
        private int $id,
        private string $text,
        private int $author,
        private int $incident,
        private int $event,
        private DateTimeImmutable $created,
        private string $creatorName,
        private string $creatorEmail,
        private CommentActionEnum $action,
        private ?DateTimeImmutable $updated = null,
        private ?string $editorName = null,
        private ?string $editorEmail = null
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

    public function getCreatorName(): string
    {
        return $this->creatorName;
    }

    public function getCreated(): DateTimeImmutable
    {
        return $this->created;
    }

    public function getCreatorEmail(): string
    {
        return $this->creatorEmail;
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

    public function getUpdated(): ?DateTimeImmutable
    {
        return $this->updated;
    }
}
