<?php

namespace App\Action\Comment;

use App\Action\Action;
use App\Action\ActionInterface;
use App\Domain\Comment\Service\NewCommentService;
use App\Domain\Event\Repository\EventRepository;
use App\Domain\Incident\Repository\IncidentRepository;
use DI\Attribute\Inject;
use Nyholm\Psr7\Response;

class NewCommentAction extends Action implements ActionInterface
{
    #[Inject]
    private NewCommentService $commentService;

    #[Inject]
    private IncidentRepository $incidentRepository;

    #[Inject]
    private EventRepository $eventRepository;

    public function action(): Response
    {
        $user = $this->getUser();
        $data = $this->getRequest()->getParsedBody();
        $incident = $this->incidentRepository->getIncident($this->getArg('incident'));
        $event = $this->eventRepository->getEvent($this->getArg('event'));
        if(!$data['text']) {
            $this->addErrorMessage("Your comment was not added to this event");
        } else {
            $message = $this->commentService->addNewComment($data, $incident, $event, $user);
            $this->addSuccessMessage($message);
        }
        return $this->redirectFor('event.view', ['incident' => $incident->getId(), 'event' => $event->getId()]);
    }
}
