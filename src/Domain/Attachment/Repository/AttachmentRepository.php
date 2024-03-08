<?php

namespace App\Domain\Attachment\Repository;

use App\Repository\Repository;

class AttachmentRepository extends Repository
{
    public string $table = 'attachment';

    public function insertNewAttachment(
        string $fileName,
        string $mimeType,
        int $uploader,
        int $incident,
        ?int $event,
        ?int $comment
    ): int {
        $queryBuilder = $this->qb();
        $queryBuilder->insert($this->table);
        $queryBuilder->values([
            'fileName' => $queryBuilder->createNamedParameter($fileName),
            'mimeType' => $queryBuilder->createNamedParameter($mimeType),
            'uploader' => $queryBuilder->createNamedParameter($uploader),
            'incident' => $queryBuilder->createNamedParameter($incident),
            'event' => $queryBuilder->createNamedParameter($event),
            'comment' => $queryBuilder->createNamedParameter($comment)
        ]);
        $queryBuilder->executeStatement($queryBuilder->getSQL());
        return $this->connection->lastInsertId();
    }

}
