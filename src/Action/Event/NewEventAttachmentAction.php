<?php

namespace App\Action\Event;

use App\Action\Action;
use App\Action\ActionInterface;
use App\Domain\Attachment\Service\AttachmentFileService;
use App\Domain\Event\Repository\EventRepository;
use App\Domain\Incident\Repository\IncidentRepository;
use DI\Attribute\Inject;
use Nyholm\Psr7\Response;

class NewEventAttachmentAction extends Action implements ActionInterface
{
    #[Inject]
    private AttachmentFileService $attachmentFileService;

    #[Inject]
    private IncidentRepository $incidentRepository;

    #[Inject]
    private EventRepository $eventRepository;

    public function action(): Response
    {
        $incident = $this->incidentRepository->getIncident($this->getArg('incident'));
        $event = $this->eventRepository->getEvent($this->getArg('event'));
        $request = $this->getRequest();
        $attachments = $this->attachmentFileService->uploadAttachments($request->getUploadedFiles(), $this->getUser(), $incident, $event, null);
        return $this->json($attachments);
    }
}
