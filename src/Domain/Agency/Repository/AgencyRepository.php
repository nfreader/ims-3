<?php

namespace App\Domain\Agency\Repository;

use App\Domain\Agency\Data\Agency;
use App\Repository\QueryBuilder;
use App\Repository\Repository;

class AgencyRepository extends Repository
{
    public ?string $entityClass = Agency::class;

    public string $table = 'agency a';

    public array $columns = [
        'a.id',
        'a.name',
        'a.logo',
        'a.created',
        'a.fullname',
        'a.location',
    ];

    public function insertNewAgency(string $name, ?string $logo, ?string $fullname, ?string $location): int
    {
        $this->insert('agency', [
            'name' => $name,
            'logo' => $logo,
            'fullname' => $fullname,
            'location' => $location
        ]);
        $pdo = $this->getPdo();
        return $pdo->lastInsertId();
    }

    public function getAgencies(): array
    {
        $sql = QueryBuilder::select(
            $this->table,
            $this->columns,
            []
        );
        return $this->run($sql)->getResults();
    }

    public function getAgency(int $id): Agency
    {
        return $this->findOneBy([$id], 'a.id = ?');
    }

}
