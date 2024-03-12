<?php

namespace App\Domain\Incident\Repository;

use App\Domain\Incident\Data\Incident;
use App\Domain\Permissions\Data\Permissions;
use App\Domain\Permissions\Data\PermissionsEnum;
use App\Domain\Permissions\Data\PermissionTypeEnum;
use App\Repository\Repository;
use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Query\QueryBuilder;

class IncidentRepository extends Repository
{
    public string $table = 'incident';

    public string $alias = 'i';

    public ?string $entityClass = Incident::class;

    public const COLUMNS = [
        'i.id',
        'i.name',
        'i.creator',
        'i.created',
        "concat_ws(' ', u.firstName, u.lastName) as creatorName",
        'u.email as creatorEmail',
        'i.role as roleId',
        'i.active'
    ];

    public function insertNewIncident(
        string $name,
        int $creator,
        ?int $role
    ): int {
        $queryBuilder = $this->qb();
        $queryBuilder->insert($this->table);
        $queryBuilder->values([
            'name' => $queryBuilder->createPositionalParameter($name),
            'creator' => $queryBuilder->createPositionalParameter($creator),
            'role' => $queryBuilder->createPositionalParameter($role)
        ]);
        $queryBuilder->executeStatement($queryBuilder->getSQL(), [$name, $creator, $role]);
        return $this->connection->lastInsertId();
    }

    private function getBaseQuery(): QueryBuilder
    {
        $queryBuilder = $this->qb();
        $queryBuilder->select(...self::COLUMNS);
        $queryBuilder->from($this->table, $this->alias);
        $queryBuilder->addSelect(...[
            'a.name as agencyName',
            'a.id as agencyId',
            'a.logo as agencyLogo'
        ]);
        $queryBuilder->leftJoin($this->alias, 'user', 'u', 'i.creator = u.id');
        $queryBuilder->leftJoin($this->alias, 'role', 'r', 'i.role = r.id');
        $queryBuilder->leftJoin($this->alias, 'agency', 'a', 'r.agency = a.id');
        $queryBuilder->orderBy('i.id asc');
        return $queryBuilder;
    }

    public function listIncidents(): array
    {
        $queryBuilder = $this->getBaseQuery();
        $result = $queryBuilder->executeQuery($queryBuilder->getSQL());
        return $this->getResults($result);
    }

    public function listIncidentsForActiveRole(?int $role): array
    {
        $queryBuilder = $this->getBaseQuery();
        $queryBuilder->leftJoin($this->alias, 'incident_permission_flags', 'f', 'f.incident = i.id AND f.type = "'.PermissionTypeEnum::ROLE->value.'"');
        $queryBuilder->where('f.target = '.$queryBuilder->createPositionalParameter($role).' and ('.PermissionsEnum::VIEW_INCIDENT->value.' & f.flags)');
        $queryBuilder->orWhere('f.target IS NULL');
        $queryBuilder->andWhere('i.active = 1');
        $queryBuilder->orderBy('i.id asc');
        $result = $queryBuilder->executeQuery($queryBuilder->getSQL(), [$role]);
        return $this->getResults($result);
    }

    public function getIncident(int $incident): Incident
    {
        $queryBuilder = $this->getBaseQuery();
        $queryBuilder->where('i.id = ' . $queryBuilder->createPositionalParameter($incident));
        $result = $queryBuilder->executeQuery($queryBuilder->getSQL(), [$incident]);
        return $this->getResult($result);
    }

    public function toggleIncident(int $id, bool $active): void
    {
        $this->updateSetting($id, 'active', !$active);
    }

    private function updateSetting(int $id, string $key, mixed $value): void
    {
        $queryBuilder = $this->qb();
        $queryBuilder->update($this->table);
        $queryBuilder->set(
            $key,
            $queryBuilder->createNamedParameter($value, ParameterType::INTEGER)
        );
        $queryBuilder->where(
            'id = '.$queryBuilder->createNamedParameter($id, ParameterType::INTEGER)
        );
        $queryBuilder->executeStatement($queryBuilder->getSQL());
    }

}
