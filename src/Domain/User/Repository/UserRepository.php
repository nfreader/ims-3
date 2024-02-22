<?php

namespace App\Domain\User\Repository;

use App\Domain\User\Data\User;
use App\Repository\DoctrineRepository;
use App\Repository\QueryBuilder;
use Doctrine\DBAL\ParameterType;
use ReflectionClass;

class UserRepository extends DoctrineRepository
{
    public string $table = 'user';
    public string $alias = 'u';

    public ?string $entityClass = User::class;

    public array $columns = [
        'u.id',
        'u.firstName',
        'u.lastName',
        'u.email',
        'u.password',
        'u.created',
        'u.created_ip as createdIp',
        'u.status'
    ];

    public array $entityMetadata = [];

    public function getUser(int $id): User
    {
        $queryBuilder = $this->qb();
        $queryBuilder->select(...$this->columns);
        $queryBuilder->from($this->table, $this->alias);
        $queryBuilder->where('u.id = ' . $queryBuilder->createPositionalParameter($id));
        $sql = $queryBuilder->getSQL();
        $result = $this->connection->executeQuery($sql, [$id]);
        $result = $result->fetchAssociative();
        return new $this->entityClass(...$this->mapRow($result));
    }

    public function getUserByEmail(string $email): ?User
    {
        $queryBuilder = $this->qb();
        $queryBuilder->select(...$this->columns);
        $queryBuilder->from($this->table, $this->alias);
        $queryBuilder->where('u.email = ' . $queryBuilder->createPositionalParameter($email));
        $sql = $queryBuilder->getSQL();
        $result = $this->connection->executeQuery($sql, [$email]);
        $result = $result->fetchAssociative();
        if(!$result) {
            return null;
        }
        return new $this->entityClass(...$this->mapRow($result));
    }

    public function getAllUsers(): array
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
            ])
        ;
        $this->connection->executeQuery($queryBuilder->getSQL(), [
            0 => $firstName,
            1 => $lastName,
            2 => $email,
            3 => $password,
            4 => ip2long($_SERVER['REMOTE_ADDR'])
        ]);
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

    // public function getAllUsers(): array
    // {
    //     $sql = QueryBuilder::select($this->table, $this->columns, []);
    //     return $this->run($sql)->getResults();
    // }
}
