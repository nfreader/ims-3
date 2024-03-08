<?php

namespace App\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Result;
use Exception;
use ReflectionClass;

class Repository
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
            } elseif (is_null($value) && $this->entityMetadata[$key]->getType()->allowsNull()) {
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

    public function getResults(Result $result, ?string $class = null): array
    {
        $results = $result->fetchAllAssociative();
        if($class) {
            $this->overrideMetadata($class);
        } else {
            $class = $this->entityClass;
        }
        foreach ($results as &$r) {
            $r = new $class(...$this->mapRow($r));
        }
        return $results;
    }

    public function getResult(Result $result, ?string $class = null): mixed
    {
        if($class) {
            $this->overrideMetadata($class);
        } else {
            $class = $this->entityClass;
        }
        return new $class(...$this->mapRow($result->fetchAssociative()));
    }

}
