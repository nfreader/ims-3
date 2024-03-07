<?php

namespace App\Domain\Incident\Service;

use App\Domain\Incident\Data\Incident;
use App\Domain\Incident\Repository\IncidentRepository;
use App\Domain\User\Data\User;
use DI\Attribute\Inject;
use Exception;

class NewIncidentService
{
    #[Inject]
    private IncidentRepository $incidentRepository;

    public function createNewIncident(array $data, User $creator): Incident
    {
        $id = $this->incidentRepository->insertNewIncident(
            $data['name'],
            $creator->getId(),
            $creator->getActiveRole()?->getRoleId()
        );
        return $this->incidentRepository->getIncident($id);
    }

}
