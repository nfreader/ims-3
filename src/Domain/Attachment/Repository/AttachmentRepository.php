<?php

namespace App\Domain\Attachment\Repository;

use App\Repository\Repository;

class AttachmentRepository extends Repository
{
    public string $table = 'attachment a';

    public function insertNewAttachment(
        string $fileName,
        string $mimeType,
        int $uploader,
        int $incident,
        ?int $event,
        ?int $comment
    ) {
        $this->insert('attachment', [
            'fileName' => $fileName,
            'mimeType' => $mimeType,
            'uploader' => $uploader,
            'incident' => $incident,
            'event' => $event,
            'comment' => $comment
        ]);
        $pdo = $this->getPdo();
        return $pdo->lastInsertId();
    }

}
