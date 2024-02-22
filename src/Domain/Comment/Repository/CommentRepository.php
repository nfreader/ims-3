<?php

namespace App\Domain\Comment\Repository;

use App\Domain\Comment\Data\Comment;
use App\Repository\QueryBuilder;
use App\Repository\Repository;
use GuzzleHttp\Psr7\Query;
use SebastianBergmann\Diff\Differ;
use SebastianBergmann\Diff\Output\StrictUnifiedDiffOutputBuilder;
use SebastianBergmann\Diff\Output\UnifiedDiffOutputBuilder;

class CommentRepository extends Repository
{
    public string $table = 'comment c';

    public ?string $entityClass = Comment::class;

    public array $columns = [
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

    public array $joins = [
        'user u ON c.author = u.id',
        'user e ON c.editor = u.id'
    ];

    public function insertNewComment(
        string $text,
        int $author,
        int $incident,
        int $event,
        string $action
    ): int {
        $this->insert('comment', [
            'text' => $text,
            'author' => $author,
            'incident' => $incident,
            'event' => $event,
            'action' => $action
        ]);
        $pdo = $this->getPdo();
        return $pdo->lastInsertId();
    }

    public function getCommentsForEvent(int $event): array
    {
        $sql = QueryBuilder::select(
            table: $this->table,
            columns: $this->columns,
            where: ['c.event = ?'],
            joins: $this->joins,
            orderBy: ['c.created' => 'ASC']
        );
        return $this->run($sql, [$event])->getResults();
    }

    public function getById(int $id): Comment
    {
        $sql = QueryBuilder::select($this->table, $this->columns, ['c.id = ?'], $this->joins, limit: '0,1');
        return $this->row($sql, [$id])->getResult();
    }

    public function updateCommentRow(int $id, string $newText, int $editor): void
    {
        $this->update('comment', [
            'text' => $newText,
            'editor' => $editor
        ], [
            'id' => $id
        ]);
    }

    public function insertCommentEdit(int $id, string $previous, string $current, int $editor): int
    {
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
        $this->insert('comment_edit', [
            'comment' => $id,
            'previous' => $previous,
            'current' => $current,
            'editor' => $editor,
            'diff' => $differ->diff($previous, $current)
        ]);
        $pdo = $this->getPdo();
        return $pdo->lastInsertId();
    }
}
