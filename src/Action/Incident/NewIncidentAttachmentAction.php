<?php

namespace App\Action\Incident;

use App\Action\Action;
use App\Action\ActionInterface;
use App\Domain\Attachment\Service\AttachmentFileService;
use App\Domain\Event\Repository\EventRepository;
use App\Domain\Incident\Repository\IncidentRepository;
use DI\Attribute\Inject;
use Nyholm\Psr7\Response;

class NewIncidentAttachmentAction extends Action implements ActionInterface
{
    #[Inject]
    private AttachmentFileService $attachmentFileService;

    #[Inject]
    private IncidentRepository $incidentRepository;

    public function action(): Response
    {
        $incident = $this->incidentRepository->getIncident($this->getArg('incident'));
        $request = $this->getRequest();
        $attachments = $this->attachmentFileService->uploadAttachments($request->getUploadedFiles(), $this->getUser(), $incident, null, null);
        return $this->json($attachments);
    }
}
