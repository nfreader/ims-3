<?php

namespace App\Action\Attachment;

use App\Action\Action;
use App\Action\ActionInterface;
use App\Domain\Attachment\Service\AttachmentFileService;
use App\Exception\UnauthorizedException;
use DI\Attribute\Inject;
use Nyholm\Psr7\Response;
use Slim\Exception\HttpException;

class NewAttachmentAction extends Action implements ActionInterface
{
    #[Inject]
    private AttachmentFileService $attachmentFileService;

    public function action(): Response
    {
        $request = $this->getRequest();
        try {
            $attachments = $this->attachmentFileService->uploadAttachments($request->getUploadedFiles(), $this->getUser(), $request->getParsedBody());
        } catch (UnauthorizedException $e) {
            throw new HttpException(
                $this->request,
                $e->getMessage(),
                $e->getCode()
            );
        }
        return $this->json($attachments);
    }
}
