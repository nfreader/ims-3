<?php

namespace App\Domain\Incident\Service;

use App\Domain\Incident\Data\Incident;
use App\Domain\Incident\Repository\IncidentRepository;
use App\Domain\User\Data\User;
use App\Service\FlashMessageService;
use DI\Attribute\Inject;
use Exception;

class NewIncidentService
{
    #[Inject]
    private IncidentRepository $incidentRepository;

    #[Inject]
    private FlashMessageService $flashService;

    public function createNewIncident(array $data, User $creator): Incident
    {
        $id = $this->incidentRepository->insertNewIncident(
            $data['name'],
            $creator->getId(),
            $creator->getActiveRole()?->getRoleId()
        );
        $this->flashService->addSuccessMessage("Your incident has been created");
        return $this->incidentRepository->getIncident($id);
    }

}
