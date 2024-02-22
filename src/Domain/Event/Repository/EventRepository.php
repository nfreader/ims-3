<?php

namespace App\Domain\Event\Repository;

use App\Domain\Event\Data\Event;
use App\Domain\Event\Exception\EventNotFoundException;
use App\Domain\Incident\Data\Incident;
use App\Repository\QueryBuilder;
use App\Repository\Repository;
use Exception;

class EventRepository extends Repository
{
    public string $table = 'event e';

    public ?string $entityClass = Event::class;

    public array $columns = [
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
        int $creator
    ): int {
        $this->insert('event', [
            'title' => $title,
            'desc' => $desc,
            'severity' => $severity,
            'incident' => $incident,
            'creator' => $creator
        ]);
        $pdo = $this->getPdo();
        return $pdo->lastInsertId();
    }

    public function getEvent(int $id): Event
    {
        $sql = QueryBuilder::select($this->table, $this->columns, ["e.id = ?"], $this->joins);
        if(!$event = $this->row($sql, [$id])->getResult()) {
            throw new EventNotFoundException();
        }
        return $event;
    }

    public function getEventsForIncident(int $incident): array
    {
        $this->columns[] = 'count(c.id) as comments';
        $this->joins[] = 'comment c ON c.event = e.id';
        $sql = QueryBuilder::select(
            $this->table,
            $this->columns,
            ['e.incident = ?'],
            $this->joins,
            orderBy: ['e.created' => 'DESC'],
            group: ['e.id']
        );
        return $this->run($sql, [$incident])->getResults();
    }

    public function listEvents(): array
    {
        $sql = QueryBuilder::select(
            $this->table,
            [
            'e.id',
            'e.title',
            'e.incident'
        ],
            [],
            orderBy: ['e.created' => 'DESC']
        );
        return $this->run($sql, [], true)->getResults();
    }

}
