<?php

namespace App\Domain\Event\Data;

use App\Domain\Event\Data\Event;
use App\Domain\Event\Data\Severity;
use App\Domain\Role\Data\RoleBadge;
use App\Domain\User\Data\UserBadge;
use DateTimeImmutable;

class EventComposite
{
    public function __construct(
        //Event
        private int $id,
        private string $title,
        private string $desc,
        private string $severity,
        private int $incident,
        private string $created,
        private int $creator,
        private string $creatorName,
        private string $creatorEmail,
        private ?string $edited = null,
        private ?int $editor = null,
        private ?string $editorName = null,
        private ?string $editorEmail = null,
        private ?int $comments = null,
        private ?string $agencyName = null,
        private ?int $agencyId = null,
        private ?string $agencyLogo = null,
        private ?string $roleName = null,
        private ?int $roleId = null
    ) {

    }

    public function getEvent(): Event
    {
        return new Event(
            id: $this->id,
            title: $this->title,
            desc: $this->desc,
            severity: Severity::tryFrom($this->severity),
            incident: $this->incident,
            created: new DateTimeImmutable($this->created),
            creator: new UserBadge(
                $this->creator,
                $this->creatorName,
                $this->creatorEmail
            ),
            role: $this->roleId ? new RoleBadge(
                $this->agencyId,
                $this->agencyName,
                $this->roleId,
                $this->roleName,
                $this->agencyLogo,
                $this->creatorName
            ) : null,
            edited: $this->edited ? new DateTimeImmutable($this->edited) : null,
            editor: $this->edited ? new UserBadge(
                $this->editor,
                $this->editorName,
                $this->editorEmail
            ) : null,
        );
    }
}
