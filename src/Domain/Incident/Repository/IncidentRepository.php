<?php

namespace App\Domain\Incident\Repository;

use App\Domain\Incident\Data\Incident;
use App\Repository\QueryBuilder;
use App\Repository\Repository;

class IncidentRepository extends Repository
{
    public string $table = 'incident i';

    public ?string $entityClass = Incident::class;

    public array $columns = [
        'i.id',
        'i.name',
        'i.creator',
        'i.created',
        "concat_ws(' ', u.firstName, u.lastName) as creatorName",
        'u.email as creatorEmail',
        'i.agency as agencyId',
    ];

    public array $joins = [
        'user u ON i.creator = u.id',
        'agency a ON i.agency = a.id'
    ];

    public function insertNewIncident(
        string $name,
        int $creator,
        ?int $agency
    ): int {
        $this->insert('incident', [
            'name' => $name,
            'creator' => $creator,
            'agency' => $agency
        ]);
        $pdo = $this->getPdo();
        return $pdo->lastInsertId();
    }

    public function listIncidents(): array
    {
        $sql = QueryBuilder::select($this->table, $this->columns, [], $this->joins);
        return $this->run($sql)->getResults();
    }

    public function getIncident(int $id): Incident
    {
        $sql = QueryBuilder::select($this->table, $this->columns, ["i.id = ?"], $this->joins);
        return $this->row($sql, [$id])->getResult();
    }

    // public function insertNewUser(
    //     string $firstName,
    //     string $lastName,
    //     string $email,
    //     string $password
    // ) {
    //     $this->insert('user', [
    //         'firstName' => $firstName,
    //         'lastName' => $lastName,
    //         'email' => $email,
    //         'password' => $password,
    //         'created_ip' => ip2long($_SERVER['REMOTE_ADDR']),
    //     ]);
    // }

}
