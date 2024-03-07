<?php

namespace App\Action\Incident;

use App\Action\Action;
use App\Domain\Event\Repository\EventRepository;
use App\Domain\Incident\Service\FetchIncidentService;
use App\Domain\Permissions\Data\PermissionsEnum;
use DI\Attribute\Inject;
use Exception;
use JustSteveKing\StatusCode\Http;
use Nyholm\Psr7\Response;
use Slim\Exception\HttpException;

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
        if(!$this->getUser()->can(PermissionsEnum::VIEW_INCIDENT, $incident)) {
            throw new HttpException($this->getRequest(), "Your active role does not have permission to view this", Http::UNAUTHORIZED->value);
        }
        $events = $this->eventRepository->getEventsForIncident($incident->getId());
        return $this->render('incident/incident.html.twig', [
            'incident' => $incident,
            'events' => $events
        ]);
    }
}
