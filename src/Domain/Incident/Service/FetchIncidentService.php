<?php

namespace App\Domain\Incident\Service;

use App\Domain\Agency\Repository\AgencyRepository;
use App\Domain\Incident\Data\Incident;
use App\Domain\Incident\Repository\IncidentPermissionsRepository;
use App\Domain\Incident\Repository\IncidentRepository;
use DI\Attribute\Inject;

class FetchIncidentService
{
    #[Inject()]
    private IncidentRepository $incidentRepository;

    #[Inject()]
    private IncidentPermissionsRepository $incidentPermissionsRepository;

    public function getIncident(int $id): Incident
    {
        $incident = $this->incidentRepository->getIncident($id);
        $incident->setPermissions(
            $this->incidentPermissionsRepository->getPermissionsForIncident($incident->getId())
        );
        return $incident;
    }

}
