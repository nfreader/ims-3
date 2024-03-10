<?php

namespace App\Action\Event;

use App\Action\ActionInterface;
use App\Action\Incident\IncidentAction;
use App\Domain\Event\Service\NewEventService;
use App\Domain\Permissions\Data\PermissionsEnum;
use DI\Attribute\Inject;
use JustSteveKing\StatusCode\Http;
use Nyholm\Psr7\Response;
use Slim\Exception\HttpException;

class NewEventAction extends IncidentAction implements ActionInterface
{
    #[Inject]
    private NewEventService $NewEventService;

    public function action(): Response
    {
        if(!$this->getUser()->can(
            PermissionsEnum::POST_UPDATES,
            $this->incident
        )) {
            throw new HttpException($this->getRequest(), "Your active role does not have permission to perform this action", Http::UNAUTHORIZED->value);
        }
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
