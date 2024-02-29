<?php

namespace App\Repository;

use App\Domain\User\Data\User;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Exception;
use ReflectionClass;

class DoctrineRepository
{
    public ?string $entityClass = null;

    public array $entityMetadata = [];

    public function __construct(public Connection $connection)
    {
        if($this->entityClass) {
            $this->getEntityMetadata($this->entityClass);
        }
    }

    private function getEntityMetadata(string $class)
    {
        $metadata = new ReflectionClass($class);
        foreach($metadata->getProperties() as $p) {
            $this->entityMetadata[$p->getName()] = $p;
        }
    }

    public function overrideMetadata(string $class): void
    {
        $this->getEntityMetadata($class);
    }

    public function qb(): QueryBuilder
    {
        return $this->connection->createQueryBuilder();
    }

    public function mapRow(array $row): mixed
    {
        array_walk($row, function (&$value, $key) {
            if(!in_array($key, array_keys($this->entityMetadata))) {
                throw new Exception("Trying to map column to entity property that was not expected! Key: {$key} Value: {$value}");
            } elseif(isset($this->entityMetadata[$key]) && $this->entityMetadata[$key]->getType()->isbuiltin()) {
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
