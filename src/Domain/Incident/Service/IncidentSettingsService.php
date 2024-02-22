<?php

namespace App\Domain\Incident\Service;

use App\Domain\Incident\Data\Incident;
use App\Domain\Incident\Repository\IncidentAgencyRepository;
use App\Domain\User\Data\User;
use DI\Attribute\Inject;
use Exception;
use JustSteveKing\StatusCode\Http;

class IncidentSettingsService
{
    #[Inject()]
    private FetchIncidentService $incidentService;

    #[Inject()]
    private IncidentAgencyRepository $incidentAgencyRepository;

    private Incident $incident;

    public function updateSetting(string $setting, array $data, int $incident, User $user)
    {

        $this->incident = $this->incidentService->getIncident($incident);

        switch ($setting) {
            default:
                throw new Exception("Invalid data", (int) Http::BAD_REQUEST);
                break;

            case 'agencies':
                $this->updateAgencies($data);
                break;
        }
    }

    private function updateAgencies(array $data)
    {
        foreach($data as $agency => $status) {
            $this->incidentAgencyRepository->updateIncidentAgencies($this->incident->getId(), $agency, $status);
        }

    }

}
