<?php

namespace App\Domain\Incident\Service;

use App\Domain\Agency\Repository\AgencyRepository;
use App\Domain\Incident\Data\Incident;
use App\Domain\Incident\Repository\IncidentRepository;
use DI\Attribute\Inject;

class FetchIncidentService
{
    #[Inject()]
    private IncidentRepository $incidentRepository;

    #[Inject()]
    private AgencyRepository $agencyRepository;

    public function getIncident(int $id): Incident
    {
        return $this->incidentRepository->getIncident($id);
    }

}
