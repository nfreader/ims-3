<?php

namespace App\Domain\Attachment\Service;

use App\Domain\Attachment\Data\UploadResult;
use App\Domain\Attachment\Repository\AttachmentRepository;
use App\Domain\Comment\Data\Comment;
use App\Domain\Event\Data\Event;
use App\Domain\Incident\Data\Incident;
use App\Domain\User\Data\User;
use League\Flysystem\Filesystem;
use League\Flysystem\Local\LocalFilesystemAdapter;
use League\Flysystem\UrlGeneration\PublicUrlGenerator;
use Nyholm\Psr7\UploadedFile;
use Psr\Container\ContainerInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\Session\Session;

class AttachmentFileService
{
    private Filesystem $filesystem;
    private Session $session;

    public function __construct(
        private ContainerInterface $container,
        private AttachmentRepository $attachmentRepository
    ) {
        $this->session = $this->container->get(Session::class);
        $this->filesystem = $container->get(Filesystem::class);
    }

    public function uploadAttachments(
        array $files,
        User $user,
        Incident $incident,
        ?Event $event = null,
        ?Comment $comment = null
    ) {
        $uploadedFiles = [];
        foreach($files['files'] as $file) {
            $uploadedFiles[] = $this->processAttachment($file, $user, $incident, $event, $comment);
        }
        return $uploadedFiles;
    }

    private function processAttachment(
        UploadedFile $file,
        User $user,
        Incident $incident,
        ?Event $event = null,
        ?Comment $comment = null
    ): UploadResult {
        $moveResult = $this->moveAndRenameFile($file);
        $moveResult->originalName = $file->getClientFilename();
        $moveResult->mimeType = $this->filesystem->mimeType($moveResult->file);
        if(!$moveResult->error) {
            $moveResult->id =  $this->addAttachmentToDatabase(
                $moveResult,
                $user,
                $incident,
                $event,
                $comment
            );
        }
        // $moveResult->file = $this->filesystem->publicUrl($moveResult->file, [
        //     'public_url' => '/uploads'
        // ]);
        return $moveResult;
    }

    private function moveAndRenameFile(UploadedFile $file): UploadResult
    {
        $info = pathinfo($file->getClientFilename());
        $newName = sprintf('%s.%s', Uuid::uuid7(), $info['extension']);
        $this->filesystem->write($newName, $file->getStream()->getContents());
        return new UploadResult(false, file: $newName);
    }

    private function addAttachmentToDatabase(
        UploadResult $file,
        User $user,
        Incident $incident,
        ?Event $event = null,
        ?Comment $comment = null
    ): int {
        return $this->attachmentRepository->insertNewAttachment(
            $file->file,
            $this->filesystem->mimeType($file->file),
            $user->getId(),
            $incident->getId(),
            $event ? $event->getId() : null,
            $comment ? $comment->getId() : null
        );
    }

}
