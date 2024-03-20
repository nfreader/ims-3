<?php 

namespace App\Domain\Incident\Data;

use App\Domain\Role\Data\RoleBadge;
use App\Domain\User\Data\UserBadge;

use DateTimeImmutable;

class IncidentComposite {
    public function __construct(
        private int $id,
        private string $name,
        private int $creator,
        private string $created,
        private string $creatorName,
        private string $creatorEmail,
        private bool $active = true,
        private ?string $agencyName = null,
        private ?int $agencyId = null,
        private ?string $agencyLogo = null,
        private ?string $roleName = null,
        private ?int $roleId = null,
    )
    {
    }

    public function getIncident(): Incident {
        return new Incident(
            id: $this->id,
            name: $this->name,
            creator: new UserBadge($this->creator, $this->creatorName, $this->creatorEmail),
            created: new DateTimeImmutable($this->created),
            active: $this->active,
            role: $this->roleId ? new RoleBadge($this->agencyId, $this->agencyName, $this->roleId, $this->roleName, $this->agencyLogo, $this->creatorName) : null
        );
    }
}