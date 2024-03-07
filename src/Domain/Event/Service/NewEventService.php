<?php

namespace App\Domain\Event\Service;

use App\Domain\Event\Repository\EventRepository;
use App\Domain\Incident\Data\Incident;
use App\Domain\User\Data\User;
use DI\Attribute\Inject;

class NewEventService
{
    #[Inject]
    private EventRepository $eventRepository;

    public function createEvent(array $data, Incident $incident, User $user)
    {
        return $this->eventRepository->insertNewEvent(
            $data['title'],
            $data['desc'],
            $data['severity'],
            $incident->getId(),
            $user->getId(),
            $user->getActiveRole()?->getRoleId()
        );

    }

}
