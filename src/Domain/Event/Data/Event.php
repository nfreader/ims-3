<?php

namespace App\Domain\Event\Data;

use DateTimeImmutable;
use App\Domain\Event\Data\Severity;
use App\Domain\Role\Data\RoleBadge;

class Event
{
    private ?RoleBadge $roleBadge = null;

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
        private ?string $agencyName = null,
        private ?int $agencyId = null,
        private ?string $agencyLogo = null,
        private ?string $roleName = null,
        private ?int $roleId = null
    ) {
        if($this->roleId) {
            $this->setRoleBadge();
        }
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

    public function setRoleBadge(): static
    {
        $this->roleBadge = new RoleBadge(
            $this->agencyId,
            $this->agencyName,
            $this->roleId,
            $this->roleName,
            $this->agencyLogo,
            $this->getCreatorName()
        );
        return $this;
    }


    public function getRoleBadge(): ?RoleBadge
    {
        return $this->roleBadge;
    }
}
