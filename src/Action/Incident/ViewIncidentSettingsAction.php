<?php

namespace App\Action\Incident;

use App\Action\Action;
use App\Action\ActionInterface;
use App\Domain\Agency\Repository\AgencyRepository;
use App\Domain\Agency\Service\FetchAgencyService;
use App\Domain\Event\Repository\EventRepository;
use App\Domain\Incident\Repository\IncidentRepository;
use App\Domain\Incident\Service\FetchIncidentService;
use DI\Attribute\Inject;
use Nyholm\Psr7\Response;

final class ViewIncidentSettingsAction extends Action implements ActionInterface
{
    #[Inject]
    private FetchIncidentService $incidentService;

    #[Inject]
    private FetchAgencyService $agencyService;

    public function action(): Response
    {
        $incident = $this->getArg('incident');
        $incident = $this->incidentService->getIncident($incident);
        return $this->render('incident/settings.html.twig', [
            'incident' => $incident,
            'agencies' => $this->agencyService->getAgenciesWithRoles()
        ]);
    }
}
