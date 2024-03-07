<?php

namespace App\Action\Incident;

use App\Action\ActionInterface;
use App\Domain\Event\Repository\EventRepository;
use DI\Attribute\Inject;
use Nyholm\Psr7\Response;

final class ViewIncidentAction extends IncidentAction implements ActionInterface
{
    #[Inject]
    private EventRepository $eventRepository;

    public function action(): Response
    {
        $events = $this->eventRepository->getEventsForIncident($this->incident->getId());
        return $this->render('incident/incident.html.twig', [
            'incident' => $this->incident,
            'events' => $events
        ]);
    }
}
