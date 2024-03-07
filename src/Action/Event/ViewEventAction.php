<?php

namespace App\Action\Event;

use App\Action\Action;
use App\Action\ActionInterface;
use App\Domain\Comment\Repository\CommentRepository;
use App\Domain\Event\Repository\EventRepository;
use App\Domain\Incident\Repository\IncidentRepository;
use DI\Attribute\Inject;
use Nyholm\Psr7\Response;

class ViewEventAction extends Action implements ActionInterface
{
    #[Inject]
    private IncidentRepository $incidentRepository;

    #[Inject]
    private EventRepository $eventRepository;

    #[Inject]
    private CommentRepository $commentRepository;

    public function action(): Response
    {
        $incident = $this->incidentRepository->getIncident($this->getArg('incident'));
        $event = $this->eventRepository->getEvent($this->getArg('event'));
        $comments = $this->commentRepository->getCommentsForEvent($event->getId());
        return $this->render('event/event.html.twig', [
            'incident' => $incident,
            'event' => $event,
            'comments' => $comments
        ]);
    }
}
