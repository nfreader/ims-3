<?php

namespace App\Action\Incident;

use App\Action\ActionInterface;
use App\Domain\Attachment\Service\AttachmentFileService;
use DI\Attribute\Inject;
use Nyholm\Psr7\Response;

class NewIncidentAttachmentAction extends IncidentAction implements ActionInterface
{
    #[Inject]
    private AttachmentFileService $attachmentFileService;

    public function action(): Response
    {
        $request = $this->getRequest();
        $attachments = $this->attachmentFileService->uploadAttachments($request->getUploadedFiles(), $this->getUser(), $this->incident, null, null);
        return $this->json($attachments);
    }
}
