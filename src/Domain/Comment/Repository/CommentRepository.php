<?php

namespace App\Domain\Comment\Repository;

use App\Domain\Comment\Data\Comment;
use App\Domain\Comment\Data\CommentComposite;
use App\Domain\Comment\Data\CommentEdit;
use App\Domain\Comment\Data\CommentEditComposite;
use App\Repository\Repository;
use Doctrine\DBAL\Query\QueryBuilder;

class CommentRepository extends Repository
{
    public string $table = 'comment';
    public string $alias = 'c';

    public ?string $entityClass = CommentComposite::class;

    public const COLUMNS = [
        'c.id',
        'c.text',
        'c.author',
        "concat_ws(' ', u.firstName, u.lastName) as authorName",
        'u.email as authorEmail',
        'c.incident',
        'c.event',
        'c.created',
        'c.action',
        'c.updated',
        'c.editor',
        "concat_ws(' ', e.firstName, e.lastName) as editorName",
        'e.email as editorEmail',
        'ar.id as authorRoleId',
        'ar.name as authorRoleName',
        'aa.id as authorAgencyId',
        'aa.name as authorAgencyName',
        'aa.logo as authorAgencyLogo',
        'er.id as editorRoleId',
        'er.name as editorRoleName',
        'ea.id as editorAgencyId',
        'ea.name as editorAgencyName',
        'ea.logo as editorAgencyLogo'
    ];

    public function insertNewComment(
        string $text,
        int $author,
        int $incident,
        int $event,
        string $action,
        int $role
    ): int {
        $queryBuilder = $this->qb();
        $queryBuilder->insert($this->table);
        $queryBuilder->values([
            'text' => $queryBuilder->createNamedParameter($text),
            'author' => $queryBuilder->createNamedParameter($author),
            'incident' => $queryBuilder->createNamedParameter($incident),
            'event' => $queryBuilder->createNamedParameter($event),
            'action' => $queryBuilder->createNamedParameter($action),
            'role' => $queryBuilder->createNamedParameter($role)
        ]);
        $queryBuilder->executeStatement();
        return $this->connection->lastInsertId();
    }

    private function getBaseQuery(): QueryBuilder
    {
        $queryBuilder = $this->qb();
        $queryBuilder->select(...self::COLUMNS);
        $queryBuilder->from($this->table, $this->alias);
        $queryBuilder->leftJoin($this->alias, 'user', 'u', 'c.author = u.id');
        $queryBuilder->leftJoin($this->alias, 'user', 'e', 'c.editor = e.id');
        //Author Role (if set)
        $queryBuilder->leftJoin($this->alias, 'role', 'ar', 'c.role = ar.id');
        $queryBuilder->leftJoin('ar', 'agency', 'aa', 'ar.agency = aa.id');

        //Editor Role (if set)
        $queryBuilder->leftJoin($this->alias, 'role', 'er', 'c.editor_role = er.id');
        $queryBuilder->leftJoin('er', 'agency', 'ea', 'er.agency = ea.id');
        return $queryBuilder;
    }

    public function getCommentsForEvent(int $event): array
    {
        $queryBuilder = $this->getBaseQuery();
        $queryBuilder->where('c.event = '.$queryBuilder->createNamedParameter($event));
        $result = $queryBuilder->executeQuery($queryBuilder->getSQL());
        return $this->getResults($result, method:'getComment');
    }

    public function getCommentById(int $id): Comment
    {
        $queryBuilder = $this->getBaseQuery();
        $queryBuilder->where('c.id = '.$queryBuilder->createNamedParameter($id));
        $result = $queryBuilder->executeQuery($queryBuilder->getSQL());
        return $this->getResult($result, method:'getComment');
    }

    public function updateCommentRow(
        int $id,
        string $newText,
        int $editor,
        ?int $editorRole = null
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
        $queryBuilder->set(
            'editor_role',
            $queryBuilder->createNamedParameter($editorRole)
        );
        $queryBuilder->where('id = '. $queryBuilder->createNamedParameter($id));
        $queryBuilder->executeStatement();
    }

    public function insertCommentEdit(
        int $id,
        string $previous,
        string $current,
        int $editor,
        ?int $role = null
    ): int {
        $queryBuilder = $this->qb();
        $queryBuilder->insert('comment_edit');
        $queryBuilder->values([
            'comment' => $queryBuilder->createNamedParameter($id),
            'previous' => $queryBuilder->createNamedParameter($previous),
            'current' => $queryBuilder->createNamedParameter($current),
            'editor' => $queryBuilder->createNamedParameter($editor),
            'role' => $queryBuilder->createNamedParameter($role)
        ]);
        $queryBuilder->executeStatement();
        return $this->connection->lastInsertId();
    }

    public function getCommentEdits(int $comment): array
    {
        $queryBuilder = $this->qb();
        $queryBuilder->select(...[
            'e.id',
            'e.comment',
            'e.current',
            'e.previous',
            'e.edited',
            'e.editor',
            "concat_ws(' ', ue.firstName, ue.lastName) as editorName",
            'ue.email as editorEmail',
            'er.id as editorRoleId',
            'er.name as editorRoleName',
            'ea.id as editorAgencyId',
            'ea.name as editorAgencyName',
            'ea.logo as editorAgencyLogo'
        ]);
        $queryBuilder->from('comment_edit', 'e');
        $queryBuilder->leftJoin('e', 'user', 'ue', 'ue.id = e.editor');
        $queryBuilder->leftJoin('e', 'role', 'er', 'e.role = er.id');
        $queryBuilder->leftJoin('er', 'agency', 'ea', 'er.agency = ea.id');
        $queryBuilder->where('e.comment = '.$queryBuilder->createNamedParameter($comment));
        $result = $queryBuilder->executeQuery($queryBuilder->getSQL());
        return $this->getResults($result, CommentEditComposite::class, 'getEdit');
    }
}
