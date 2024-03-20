<?php

namespace App\Domain\Comment\Service;

use App\Domain\Comment\Repository\CommentRepository;
use App\Domain\Event\Data\Event;
use App\Domain\Event\Repository\EventRepository;
use App\Domain\Event\Service\EditEventService;
use App\Domain\Incident\Data\Incident;
use App\Domain\User\Data\User;
use DI\Attribute\Inject;

class NewCommentService
{
    #[Inject]
    private CommentRepository $commentRepository;

    #[Inject]
    private EditEventService $editEventService;

    public function addNewComment(array $data, Incident $incident, Event $event, User $author): string
    {
        $message = "Your comment has been added to this event";
        switch($data['action']) {
            default:
            case 'comment':
                # Nothing to do here
                $action = 'comment';
                break;

            case 'prepend':
                $this->editEventService->prependComment($event, $data['text'], $author);
                $message = "This event has been updated";
                $action = 'prepend';
                break;

            case 'append':
                $this->editEventService->appendComment($event, $data['text'], $author);
                $message = "This event has been updated";
                $action = 'append';
                break;

            case 'replace':
                $this->editEventService->replaceEventText($event, $data['text'], $author);
                $message = "This event has been updated";
                $action = 'replace';
                break;
        }
        $this->commentRepository->insertNewComment(
            $data['text'],
            $author->getId(),
            $incident->getId(),
            $event->getId(),
            $action,
            $author->getActiveRole()?->getRoleId()
        );
        return $message;
    }

}
