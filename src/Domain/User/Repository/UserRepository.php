<?php

namespace App\Domain\User\Repository;

use App\Domain\User\Data\User;
use App\Domain\User\Data\UserComposite;
use App\Repository\Repository;
use App\Repository\QueryBuilder;
use Doctrine\DBAL\ParameterType;
use ReflectionClass;

class UserRepository extends Repository
{
    public string $table = 'user';
    public string $alias = 'u';

    public ?string $entityClass = UserComposite::class;

    public const COLUMNS = [
        'u.id',
        'u.firstName',
        'u.lastName',
        'u.email',
        'u.password',
        'u.created',
        'u.created_ip as createdIp',
        'u.status',
        'u.is_admin as isAdmin',
    ];

    public array $entityMetadata = [];

    public function getUser(int $id): User
    {
        $queryBuilder = $this->qb();
        $queryBuilder->select(...self::COLUMNS);
        $queryBuilder->from($this->table, $this->alias);
        $queryBuilder->where('u.id = ' . $queryBuilder->createPositionalParameter($id));
        $sql = $queryBuilder->getSQL();
        $result = $this->connection->executeQuery($sql, [$id]);
        return $this->getResult($result, method:'getUser');
    }

    public function getUserByEmail(string $email): ?User
    {
        $queryBuilder = $this->qb();
        $queryBuilder->select(...self::COLUMNS);
        $queryBuilder->from($this->table, $this->alias);
        $queryBuilder->where('u.email = ' . $queryBuilder->createPositionalParameter($email));
        $sql = $queryBuilder->getSQL();
        $result = $this->connection->executeQuery($sql, [$email]);
        return $this->getResult($result, method:'getUser');
    }

    public function getAllUsers(): array
    {
        $queryBuilder = $this->qb();
        $queryBuilder->select(...self::COLUMNS);
        $queryBuilder->from($this->table, $this->alias);
        $queryBuilder->addGroupBy('u.id');
        $queryBuilder->addOrderBy('u.lastName', 'ASC');
        $queryBuilder->addOrderBy('u.firstName', 'ASC');
        $result = $this->connection->executeQuery($queryBuilder->getSQL());
        return $this->getResults($result, method:'getUser');
    }

    public function insertNewUser(
        string $firstName,
        string $lastName,
        string $email,
        string $password
    ): void {
        $queryBuilder = $this->qb();
        $queryBuilder->insert($this->table)
            ->values([
                'firstName' => $queryBuilder->createPositionalParameter($firstName),
                'lastName' => $queryBuilder->createPositionalParameter($lastName),
                'email' => $queryBuilder->createPositionalParameter($email),
                'password' => $queryBuilder->createPositionalParameter($password),
                'created_ip' => $queryBuilder->createPositionalParameter('ip', ParameterType::INTEGER)
            ]);
        $this->connection->executeQuery($queryBuilder->getSQL(), [
            0 => $firstName,
            1 => $lastName,
            2 => $email,
            3 => $password,
            4 => ip2long($_SERVER['REMOTE_ADDR'])
        ]);
    }

    public function setPassword(string $password, int $user)
    {
        $this->updateUser('password', $password, $user);
    }

    private function updateUser(string $key, string|int|bool $value, int $user)
    {
        $queryBuilder = $this->qb();
        $queryBuilder->update($this->table)
        ->set($key, $queryBuilder->createNamedParameter($value))
        ->where('id = '.$queryBuilder->createNamedParameter($user));
        $queryBuilder->executeStatement();
    }
}
