<?php

namespace App\Action\Incident;

use App\Action\Action;
use App\Domain\Event\Repository\EventRepository;
use App\Domain\Incident\Repository\IncidentRepository;
use App\Domain\Incident\Service\FetchIncidentService;
use DI\Attribute\Inject;
use Nyholm\Psr7\Response;

final class ViewIncidentAction extends Action
{
    #[Inject]
    private FetchIncidentService $incidentService;

    #[Inject]
    private EventRepository $eventRepository;

    public function action(): Response
    {
        $incident = $this->getArg('incident');
        $incident = $this->incidentService->getIncident($incident);
        $events = $this->eventRepository->getEventsForIncident($incident->getId());
        return $this->render('incident/incident.html.twig', [
            'incident' => $incident,
            'events' => $events
        ]);
    }
}
