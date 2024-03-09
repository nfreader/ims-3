<?php

namespace App\Domain\Event\Service;

use App\Domain\Event\Data\Event;
use App\Domain\Event\Repository\EventRepository;

class FetchEventService
{
    public function __construct(
        private EventRepository $eventRepository
    ) {

    }

    public function getEvent(int $id): Event
    {
        return $this->eventRepository->getEvent($id);
    }

}
