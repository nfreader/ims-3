<?php

namespace App\Domain\Agency\Repository;

use App\Domain\Agency\Data\Agency;
use App\Repository\DoctrineRepository;
use App\Repository\QueryBuilder;
use App\Repository\Repository;

class AgencyRepository extends DoctrineRepository
{
    public ?string $entityClass = Agency::class;

    public string $table = 'agency';

    public string $alias = 'a';

    public array $columns = [
        'a.id',
        'a.name',
        'a.logo',
        'a.created',
        'a.fullname',
        'a.location',
        'a.active'
    ];

    public function insertNewAgency(
        string $name,
        ?string $logo,
        ?string $fullname,
        ?string $location
    ): int {
        $queryBuilder = $this->qb();
        $queryBuilder->insert($this->table)
        ->values([
            'name' => $queryBuilder->createPositionalParameter($name),
            'logo' => $queryBuilder->createPositionalParameter($logo),
            'fullName' => $queryBuilder->createPositionalParameter($fullname),
            'location' => $queryBuilder->createPositionalParameter($location)
        ]);
        $this->connection->executeQuery($queryBuilder->getSQL(), [
            0 => $name,
            1 => $logo,
            2 => $fullname,
            3 => $location
        ]);
        return $this->connection->lastInsertId();
    }

    public function getAgencies(): array
    {
        $queryBuilder = $this->qb();
        $queryBuilder->select(...$this->columns);
        $queryBuilder->from($this->table, $this->alias);
        $result = $this->connection->executeQuery($queryBuilder->getSQL());
        $result = $result->fetchAllAssociative();
        foreach($result as &$r) {
            $r = new $this->entityClass(...$this->mapRow($r));
        }
        return $result;
    }

    public function getAgency(int $id): ?Agency
    {
        $queryBuilder = $this->qb();
        $queryBuilder->select(...$this->columns);
        $queryBuilder->select(...$this->columns);
        $queryBuilder->from($this->table, $this->alias);
        $queryBuilder->where('a.id = '. $queryBuilder->createNamedParameter($id));
        $result = $queryBuilder->executeQuery($queryBuilder->getSQL(), [$id]);
        $result = $result->fetchAssociative();
        if(!$result) {
            return null;
        }
        return new $this->entityClass(...$this->mapRow($result));
    }

    public function updateAgency(int $id, string $name, ?string $fullname, ?string $location, ?string $logo): int
    {
        $queryBuilder = $this->qb();
        $queryBuilder->update($this->table);
        $queryBuilder->set('name', $queryBuilder->createPositionalParameter($name));
        $queryBuilder->set('fullname', $queryBuilder->createPositionalParameter($fullname));
        $queryBuilder->set('location', $queryBuilder->createPositionalParameter($location));
        $queryBuilder->set('logo', $queryBuilder->createPositionalParameter($logo));
        $queryBuilder->where('id = '. $queryBuilder->createPositionalParameter($id));
        $result = $queryBuilder->executeQuery($queryBuilder->getSQL(), [$name, $fullname, $location, $logo, $id]);
        return $result->rowCount();
    }

}
