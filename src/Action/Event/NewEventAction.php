<?php

namespace App\Action\Event;

use App\Action\ActionInterface;
use App\Action\Incident\IncidentAction;
use App\Domain\Event\Service\NewEventService;
use DI\Attribute\Inject;
use Nyholm\Psr7\Response;

class NewEventAction extends IncidentAction implements ActionInterface
{
    #[Inject]
    private NewEventService $NewEventService;

    public function action(): Response
    {
        $this->NewEventService->createEvent(
            $this->getRequest()->getParsedBody(),
            $this->incident,
            $this->getUser()
        );
        $this->addSuccessMessage("Your event has been added to this incident");
        return $this->redirectFor(
            'incident.view',
            ['incident' => $this->incident->getId()]
        );
    }
}
