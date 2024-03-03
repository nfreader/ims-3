<?php

namespace App\Domain\Incident\Repository;

use App\Domain\Incident\Data\Incident;
use App\Domain\Permissions\Data\PermissionTypeEnum;
use App\Domain\Permissions\Data\Permissions;
use App\Repository\DoctrineRepository;

class IncidentPermissionsRepository extends DoctrineRepository
{
    public string $table = 'incident_permission_flags';

    public string $alias = 'p';

    public ?string $entityClass = Permissions::class;

    public function insertPermissions(PermissionTypeEnum $type, int $target, int $flags, int $incident)
    {
        //Delete the existing row (if it exists)
        $queryBuilder = $this->qb();
        $queryBuilder->delete($this->table);
        $queryBuilder->where('incident = '. $queryBuilder->createPositionalParameter($incident));
        $queryBuilder->andWhere('type = '. $queryBuilder->createPositionalParameter($type->value));
        $queryBuilder->andWhere('target = '. $queryBuilder->createPositionalParameter($target));
        $queryBuilder->executeQuery($queryBuilder->getSQL(), [$incident, $type->value, $target]);

        //Insert a new row
        $queryBuilder = $this->qb();
        $queryBuilder->insert($this->table);
        $queryBuilder->values([
            'incident' => $queryBuilder->createPositionalParameter($incident),
            'target' => $queryBuilder->createPositionalParameter($target),
            'type' => $queryBuilder->createPositionalParameter($type->value),
            'flags' => $queryBuilder->createPositionalParameter($flags)
        ]);
        $queryBuilder->executeStatement($queryBuilder->getSQL(), [$incident, $target, $type->value, $flags]);
    }

    public function getPermissionsForIncident(int $incident)
    {
        $queryBuilder = $this->qb();
        $queryBuilder->from('incident_permission_flags', 'f');
        $queryBuilder->select(...[
            'f.flags',
            'f.target',
            'f.type',
            'f.incident'
        ]);
        $queryBuilder->where('f.incident = '.$queryBuilder->createPositionalParameter($incident));
        $result = $queryBuilder->executeQuery($queryBuilder->getSQL(), [$incident]);
        return $this->getResults($result);
    }


}
