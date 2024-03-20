<?php

namespace App\Action\Comment;

use App\Action\ActionInterface;
use App\Action\Incident\IncidentAction;
use App\Domain\Comment\Service\FetchCommentService;
use App\Domain\Event\Repository\EventRepository;
use DI\Attribute\Inject;
use Nyholm\Psr7\Response;

class ViewCommentAction extends IncidentAction implements ActionInterface
{
    #[Inject]
    private FetchCommentService $commentService;

    #[Inject]
    private EventRepository $eventRepository;

    public function action(): Response
    {
        $this->addContext(
            'events',
            $this->eventRepository->getEventsForIncident($this->incident->getId())
        );
        $this->addContext(
            'event',
            $this->eventRepository->getEvent($this->getArg('event'))
        );
        $this->addContext(
            'comment',
            $this->commentService->getComment($this->getArg('comment'))
        );
        return $this->render('comment/comment.html.twig');
    }
}
