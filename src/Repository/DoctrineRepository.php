<?php

namespace App\Repository;

use App\Domain\User\Data\User;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use ReflectionClass;

class DoctrineRepository
{
    public ?string $entityClass = User::class;

    public array $entityMetadata = [];

    public function __construct(public Connection $connection)
    {
        $this->getEntityMetadata();
    }

    private function getEntityMetadata()
    {
        $metadata = new ReflectionClass($this->entityClass);
        foreach($metadata->getProperties() as $p) {
            $this->entityMetadata[$p->getName()] = $p;
        }
    }

    public function qb(): QueryBuilder
    {
        return $this->connection->createQueryBuilder();
    }

    public function mapRow(array $row): mixed
    {
        array_walk($row, function (&$value, $key) {
            if($this->entityMetadata[$key]->getType()->isbuiltin()) {
                return;
            } else {
                $class = new ReflectionClass($this->entityMetadata[$key]->getType()->getName());
                if($class->isEnum()) {
                    $value = call_user_func($class->getName()."::tryFrom", $value);
                } else {
                    $value = new($class->getName())($value);
                }
            }
        });
        return $row;
    }

}
