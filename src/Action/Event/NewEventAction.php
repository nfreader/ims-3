<?php

namespace App\Action\Event;

use App\Action\Action;
use App\Domain\Event\Service\NewEventService;
use App\Domain\Incident\Repository\IncidentRepository;
use DI\Attribute\Inject;
use Nyholm\Psr7\Response;

class NewEventAction extends Action
{
    #[Inject]
    private NewEventService $NewEventService;

    #[Inject]
    private IncidentRepository $incidentRepository;

    public function action(): Response
    {
        $user = $this->getUser();
        $incident = $this->incidentRepository->getIncident($this->getArg('incident'));
        $this->NewEventService->createEvent($this->getRequest()->getParsedBody(), $incident, $user);
        $this->addSuccessMessage("Your event has been added to this incident");
        return $this->redirectFor('incident.view', ['incident' => $incident->getId()]);
    }
}
