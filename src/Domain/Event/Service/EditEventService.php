<?php

namespace App\Domain\Event\Service;

use App\Domain\Event\Data\Event;
use App\Domain\Event\Repository\EventRepository;
use App\Domain\User\Data\User;
use DateTime;
use DI\Attribute\Inject;

class EditEventService
{
    #[Inject()]
    private EventRepository $eventRepository;

    public function prependComment(Event $event, string $text, User $author)
    {
        $newText = sprintf(
            "**UPDATED BY** %s %s at %s:\n\n%s\n\n---\n\n%s",
            $author->getName(),
            $author->getEmail(),
            (new DateTime('now'))->format("F j, Y \a\t H:i:s (e)"),
            $text,
            $event->getDesc()
        );
        $this->replaceEventText($event, $newText, $author);
    }

    public function appendComment(Event $event, string $text, User $author)
    {
        $newText = sprintf(
            "%s\n\n---\n\n**UPDATED BY** %s %s at %s:\n\n%s\n\n",
            $event->getDesc(),
            $author->getName(),
            $author->getEmail(),
            (new DateTime('now'))->format("F j, Y \a\t H:i:s (e)"),
            $text,
        );
        $this->replaceEventText($event, $newText, $author);
    }

    public function replaceEventText(Event $event, string $text, User $author)
    {
        // $this->eventRepository->updateEvent($event->getId(), [
        //     'event_text' => $text,
        //     'editor' => $author->getId()
        // ]);
    }

}
