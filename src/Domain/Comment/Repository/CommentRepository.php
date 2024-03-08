<?php

namespace App\Domain\Comment\Repository;

use App\Domain\Comment\Data\Comment;
use App\Repository\Repository;
use Doctrine\DBAL\Query\QueryBuilder;
use GuzzleHttp\Psr7\Query;
use SebastianBergmann\Diff\Differ;
use SebastianBergmann\Diff\Output\StrictUnifiedDiffOutputBuilder;
use SebastianBergmann\Diff\Output\UnifiedDiffOutputBuilder;

class CommentRepository extends Repository
{
    public string $table = 'comment';
    public string $alias = 'c';

    public ?string $entityClass = Comment::class;

    public const COLUMNS = [
        'c.id',
        'c.text',
        'c.author',
        'c.incident',
        'c.event',
        'c.created',
        'c.action',
        "concat_ws(' ', u.firstName, u.lastName) as creatorName",
        'u.email as creatorEmail',
        'c.updated',
        "concat_ws(' ', e.firstName, e.lastName) as editorName",
        'e.email as editorEmail'
    ];

    public function insertNewComment(
        string $text,
        int $author,
        int $incident,
        int $event,
        string $action
    ): int {
        $queryBuilder = $this->qb();
        $queryBuilder->insert($this->table);
        $queryBuilder->values([
            'text' => $queryBuilder->createNamedParameter($text),
            'author' => $queryBuilder->createNamedParameter($author),
            'incident' => $queryBuilder->createNamedParameter($incident),
            'event' => $queryBuilder->createNamedParameter($event),
            'action' => $queryBuilder->createNamedParameter($action)
        ]);
        $queryBuilder->executeStatement($queryBuilder->getSQL());
        return $this->connection->lastInsertId();
    }

    private function getBaseQuery(): QueryBuilder
    {
        $queryBuilder = $this->qb();
        $queryBuilder->select(...self::COLUMNS);
        $queryBuilder->from($this->table, $this->alias);
        $queryBuilder->leftJoin($this->alias, 'user', 'u', 'c.author = u.id');
        $queryBuilder->leftJoin($this->alias, 'user', 'e', 'c.editor = e.id');
        return $queryBuilder;
    }

    public function getCommentsForEvent(int $event): array
    {
        $queryBuilder = $this->getBaseQuery();
        $queryBuilder->where('c.event = '.$queryBuilder->createNamedParameter($event));
        $result = $queryBuilder->executeQuery($queryBuilder->getSQL());
        return $this->getResults($result);
    }

    public function getCommentById(int $id): Comment
    {
        $queryBuilder = $this->getBaseQuery();
        $queryBuilder->where('c.id = '.$queryBuilder->createNamedParameter($id));
        $result = $queryBuilder->executeQuery($queryBuilder->getSQL());
        return $this->getResult($result);
    }

    public function updateCommentRow(
        int $id,
        string $newText,
        int $editor
    ): void {
        $queryBuilder = $this->qb();
        $queryBuilder->update($this->table);
        $queryBuilder->set(
            'text',
            $queryBuilder->createNamedParameter($newText)
        );
        $queryBuilder->set(
            'editor',
            $queryBuilder->createNamedParameter($editor)
        );
        $queryBuilder->where('id = '. $queryBuilder->createNamedParameter($id));
        $queryBuilder->executeStatement($queryBuilder->getSQL());
    }

    public function insertCommentEdit(
        int $id,
        string $previous,
        string $current,
        int $editor
    ): int {
        $builder = new StrictUnifiedDiffOutputBuilder([
            'collapseRanges'      => true,
            'commonLineThreshold' => 6,
            'contextLines'        => 3,
            'fromFile'            => '',
            'fromFileDate'        => null,
            'toFile'              => '',
            'toFileDate'          => null,
        ]);
        $differ = new Differ($builder);
        $diff = $differ->diff($previous, $current);
        $queryBuilder = $this->qb();
        $queryBuilder->insert('comment_edit');
        $queryBuilder->values([
            'comment' => $queryBuilder->createNamedParameter($id),
            'previous' => $queryBuilder->createNamedParameter($previous),
            'current' => $queryBuilder->createNamedParameter($current),
            'editor' => $queryBuilder->createNamedParameter($editor),
            'diff' => $queryBuilder->createNamedParameter($diff)
        ]);
        $queryBuilder->executeStatement($queryBuilder->getSQL());
        return $this->connection->lastInsertId();
    }

    // public function updateCommentRow(int $id, string $newText, int $editor): void
    // {
    //     $this->update('comment', [
    //         'text' => $newText,
    //         'editor' => $editor
    //     ], [
    //         'id' => $id
    //     ]);
    // }

    // public function insertCommentEdit(int $id, string $previous, string $current, int $editor): int
    // {

    //     $this->insert('comment_edit', [
    //         'comment' => $id,
    //         'previous' => $previous,
    //         'current' => $current,
    //         'editor' => $editor,
    //         'diff' => $differ->diff($previous, $current)
    //     ]);
    //     $pdo = $this->getPdo();
    //     return $pdo->lastInsertId();
    // }
}
