<?php

namespace App\Action\Incident;

use App\Action\Action;
use App\Action\ActionInterface;
use App\Domain\Event\Repository\EventRepository;
use App\Domain\Incident\Repository\IncidentRepository;
use DI\Attribute\Inject;
use Nyholm\Psr7\Response;

final class ListIncidentsAction extends Action implements ActionInterface
{
    #[Inject]
    private IncidentRepository $incidentRepository;

    #[Inject]
    private EventRepository $eventRepository;

    public function action(): Response
    {
        $role = $this->getUser()->getActiveRole()?->getRoleId();
        if(!$this->getUser()->isSudoMode()) {
            $incidents = $this->incidentRepository->listIncidentsForActiveRole($role);
        } else {
            $incidents = $this->incidentRepository->listIncidents();
        }
        $events = $this->eventRepository->listEvents();
        return $this->json([
            'incidents' => $incidents,
            'events' => $events
        ]);
    }
}
