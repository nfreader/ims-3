<?php

namespace App\Domain\Event\Repository;

use App\Domain\Event\Data\Event;
use App\Domain\Event\Exception\EventNotFoundException;
use App\Repository\DoctrineRepository;
use App\Repository\QueryBuilder;
use App\Repository\Repository;

class EventRepository extends DoctrineRepository
{
    public string $table = 'event';
    public string $alias = 'e';

    public ?string $entityClass = Event::class;

    public const COLUMNS = [
        'e.id',
        'e.title',
        'e.desc',
        'e.severity',
        'e.incident',
        'e.creator',
        'e.created',
        "concat_ws(' ', u.firstName, u.lastName) as creatorName",
        'u.email as creatorEmail',
        'e.edited',
        "concat_ws(' ', edit.firstName, edit.lastName) as editorName",
        'edit.email as editorEmail',
    ];

    public array $joins = [
        'user u ON e.creator = u.id',
        'user edit ON e.editor = edit.id',
    ];

    public function insertNewEvent(
        string $title,
        string $desc,
        string $severity,
        int $incident,
        int $creator,
        int $role
    ): int {
        $queryBuilder = $this->qb();
        $queryBuilder->insert($this->table);
        $queryBuilder->values([
            'title' => $queryBuilder->createNamedParameter($title),
            '`desc`' => $queryBuilder->createNamedParameter($desc),
            'severity' => $queryBuilder->createNamedParameter($severity),
            'incident' => $queryBuilder->createNamedParameter($incident),
            'creator' => $queryBuilder->createNamedParameter($creator),
            'role' => $queryBuilder->createNamedParameter($role)
        ]);
        $queryBuilder->executeStatement($queryBuilder->getSQL());
        return $this->connection->lastInsertId();
    }

    public function getEvent(int $event): Event
    {
        $queryBuilder = $this->qb();
        $queryBuilder->from($this->table, $this->alias);
        $queryBuilder->select(...self::COLUMNS);
        $queryBuilder->addSelect('count(c.id) as comments');
        $queryBuilder->leftJoin($this->alias, 'user', 'u', 'e.creator = u.id');
        $queryBuilder->leftJoin($this->alias, 'user', 'edit', 'e.editor = edit.id');
        $queryBuilder->leftJoin($this->alias, 'comment', 'c', 'c.event = e.id');
        $queryBuilder->where('e.id = '.$queryBuilder->createNamedParameter($event));
        $result = $queryBuilder->executeQuery($queryBuilder->getSQL());
        return $this->getResult($result);
    }

    public function getEventsForIncident(int $incident): array
    {
        $queryBuilder = $this->qb();
        $queryBuilder->from($this->table, $this->alias);
        $queryBuilder->select(...self::COLUMNS);
        $queryBuilder->addSelect('count(c.id) as comments');
        $queryBuilder->leftJoin($this->alias, 'user', 'u', 'e.creator = u.id');
        $queryBuilder->leftJoin($this->alias, 'user', 'edit', 'e.editor = edit.id');
        $queryBuilder->leftJoin($this->alias, 'comment', 'c', 'c.event = e.id');
        $queryBuilder->where('e.incident = '.$queryBuilder->createNamedParameter($incident));
        $queryBuilder->addGroupBy('e.id');
        $queryBuilder->addOrderBy('e.created DESC');
        $result = $queryBuilder->executeQuery($queryBuilder->getSQL());
        return $this->getResults($result);
    }

    public function listEvents(): array
    {
        $queryBuilder = $this->qb();
        $queryBuilder->from($this->table, $this->alias);
        $queryBuilder->select(...['e.id','e.title', 'e.incident']);
        $queryBuilder->addOrderBy('e.created DESC');
        $result = $queryBuilder->executeQuery($queryBuilder->getSQL());
        return $result->fetchAllAssociative();
    }
}
