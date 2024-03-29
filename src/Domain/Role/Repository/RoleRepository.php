<?php

namespace App\Domain\Role\Repository;

use App\Domain\Permissions\Data\PermissionTypeEnum;
use App\Domain\Role\Data\Role;
use App\Domain\Role\Data\UserRole;
use App\Domain\User\Data\User;
use App\Domain\User\Repository\UserRepository;
use App\Repository\Repository;
use Doctrine\DBAL\ParameterType;
use Exception;

/**
 * RoleRepository
 *
 * Methods for interacting with the `role` table and making changes to the `user_role` table. Note that user roles are SOFT DELETED.
 *
 */
class RoleRepository extends Repository
{
    private string $table = 'role';
    private string $alias = 'r';

    public ?string $entityClass = Role::class;

    public const COLUMNS = [
        'r.id',
        'r.name',
        'r.agency',
        'r.created',
        'r.active'
    ];

    public function insertNewRole(int $agency, string $name): int
    {
        $queryBuilder = $this->qb();
        $queryBuilder->insert($this->table);
        $queryBuilder->values([
            'agency' => $queryBuilder->createPositionalParameter($agency),
            'name' => $queryBuilder->createPositionalParameter($name)
        ]);
        $queryBuilder->executeStatement($queryBuilder->getSQL(), [$agency, $name]);
        return $this->connection->lastInsertId();
    }

    public function getRolesForAgency(int $agency): array
    {
        $queryBuilder = $this->qb();
        $queryBuilder->select(...self::COLUMNS);
        $queryBuilder->addSelect('count(ur.id) as users');
        $queryBuilder->leftJoin(
            $this->alias,
            'user_role',
            'ur',
            'ur.role = r.id and ur.active = 1'
        );
        $queryBuilder->from($this->table, $this->alias);
        $queryBuilder->where('r.agency = '.$queryBuilder->createPositionalParameter($agency));
        $queryBuilder->addGroupBy('r.id');
        $result = $queryBuilder->executeQuery($queryBuilder->getSQL(), [$agency]);
        $result = $result->fetchAllAssociative();
        foreach ($result as &$r) {
            $r = new $this->entityClass(...$this->mapRow($r));
        }
        return $result;
    }

    public function getRole(int $role): Role
    {
        $queryBuilder = $this->qb();
        $queryBuilder->select(...self::COLUMNS);
        $queryBuilder->from($this->table, $this->alias);
        $queryBuilder->where('r.id = '.$queryBuilder->createPositionalParameter($role));
        $result = $queryBuilder->executeQuery($queryBuilder->getSQL(), [$role]);
        return new $this->entityClass(
            ...$this->mapRow($result->fetchAssociative())
        );
    }

    public function getAllRoles(): array
    {
        $queryBuilder = $this->qb();
        $queryBuilder->select(...self::COLUMNS);
        $queryBuilder->from($this->table, $this->alias);
        $queryBuilder->where('r.active = 1');
        $result = $queryBuilder->executeQuery($queryBuilder->getSQL());
        return $this->getResults($result);
    }

    public function updateRole(int $role, array $data)
    {
        $queryBuilder = $this->qb();
        $queryBuilder->update($this->table);
        foreach($data as $key => $value) {
            $queryBuilder->set(
                $key,
                $queryBuilder->createPositionalParameter($value)
            );
        }
        $queryBuilder->where(
            'id = '.$queryBuilder->createPositionalParameter($role, ParameterType::INTEGER)
        );
        $result = $queryBuilder->executeQuery($queryBuilder->getSQL(), [...array_values($data),$role]);
    }

    public function insertOrUpdateMembership(int $target, int $role)
    {
        try {
            return $this->insertUserRole($target, $role);
        } catch (Exception $e) {
            return $this->updateUserRole($target, $role);
        }
    }

    private function updateUserRole(int $target, int $role)
    {
        $queryBuilder = $this->qb();
        $queryBuilder->update('user_role');
        $queryBuilder->set('active', '1 - active');
        $queryBuilder->where('user = ' .$queryBuilder->createPositionalParameter($target));
        $queryBuilder->andWhere('role = '. $queryBuilder->createPositionalParameter($role));
        return $queryBuilder->executeStatement($queryBuilder->getSQL(), [$target, $role]);
    }

    private function insertUserRole(int $target, int $role): int
    {
        $queryBuilder = $this->qb();
        $queryBuilder->insert('user_role');
        $queryBuilder->values([
            'user' => $queryBuilder->createPositionalParameter($target),
            'role' => $queryBuilder->createPositionalParameter($role)
        ]);
        $queryBuilder->executeStatement($queryBuilder->getSQL(), [$target, $role]);
        return $this->connection->lastInsertId();
    }

    /**
     * getUsersForRole
     *
     * Returns an array of User objects of users that are currently assigned to the given $role
     *
     * @param integer $role
     * @return array
     */
    public function getUsersForRole(int $role): array
    {
        $queryBuilder = $this->qb();
        $queryBuilder->select(...UserRepository::COLUMNS);
        $queryBuilder->from('user_role', 'ur');
        $queryBuilder->join('ur', 'user', 'u', 'ur.user = u.id');
        $queryBuilder->where('ur.role = '. $queryBuilder->createPositionalParameter($role));
        $queryBuilder->andWhere('ur.active = 1');
        $result = $queryBuilder->executeQuery($queryBuilder->getSQL(), [$role]);
        $this->overrideMetadata(User::class);
        $return = [];
        foreach($result->fetchAllAssociative() as $r) {
            $return[] = new User(...$this->mapRow($r));
        }
        return $return;
    }

    /**
     * getRolesForUser
     *
     * Returns and array of UserRole objects of roles that are currently assigned to the given $user
     *
     * @param integer $user
     * @return array
     */
    public function getRolesForUser(int $user): array
    {
        $queryBuilder = $this->qb();
        $queryBuilder->select(...[
            'r.id as roleId',
            'r.name as roleName',
            'a.id as agencyId',
            'a.name as agencyName',
            'a.logo as agencyLogo',
            'f.flags',
            'f.incident'
        ]);
        $queryBuilder->from('user_role', 'ur');
        $queryBuilder->leftjoin('ur', 'role', 'r', 'ur.role = r.id');
        $queryBuilder->leftjoin('r', 'agency', 'a', 'r.agency = a.id');
        $queryBuilder->leftjoin('r', 'incident_permission_flags', 'f', "r.id = f.target AND f.type = '".PermissionTypeEnum::ROLE->value."'");
        $queryBuilder->where('ur.user = '.$queryBuilder->createPositionalParameter($user));
        $queryBuilder->andWhere('ur.active = 1');
        $queryBuilder->addGroupBy('r.id');
        $result = $queryBuilder->executeQuery($queryBuilder->getSQL(), [$user]);
        return $this->getResults($result, UserRole::class);
    }

}
