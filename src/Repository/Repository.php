<?php

namespace App\Repository;

use App\Factory\LoggerFactory;
use Exception;
use Monolog\Formatter\HtmlFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use ParagonIE\EasyDB\EasyDB;
use PDO;
use Psr\Container\ContainerInterface;
use ReflectionClass;

class Repository
{
    protected EasyDB $db;

    public const PER_PAGE = 60;

    private mixed $result;

    public ?string $entityClass = null;

    protected array $entityMetadata = [];

    public string $table;

    public array $columns = [];

    public array $where = [];

    public array $joins = [];

    public array $order = [];

    public ?array $groupBy = [];

    public ?string $limit = '0, 100';

    private int $pages = 0;

    private Logger $logger;

    private string $requestId;

    public function __construct(
        protected ContainerInterface $container,
    ) {
        $this->db = $container->get(EasyDB::class);
        $this->requestId = $container->get('request_id');
        $this->fetchEntityMetadata();
        $logger = $container->get(LoggerFactory::class)
        ->addFileHandler('db.log')
        ->createLogger('db_log');
        $this->logger = $logger;
    }

    public function run(
        string $statement,
        array $params = [],
        bool $skipParse = false
    ): static {
        $this->logQuery($statement, debug_backtrace(3, 5));
        $this->setResults($this->db->run($statement, ...$params), $skipParse);
        return $this;
    }

    public function row(
        string $statement,
        array $params = [],
        bool $skipParse = false
    ): static {
        $this->logQuery($statement, debug_backtrace(3, 5));
        $this->setResult($this->db->row($statement, ...$params), $skipParse);
        return $this;
    }

    public function insert(
        string $table,
        array $map
    ) {
        return $this->db->insert($table, $map);
    }

    public function update(
        string $table,
        array $changes,
        array $conditions
    ) {
        return $this->db->update($table, $changes, $conditions);
    }

    public function directQuery(
        string $table,
        array $columns,
        array $where = [],
        array $joins = [],
        array $order = [],
        array $group = [],
        ?string $limit = null
    ) {
        $this->table = $table;
        $this->columns = $columns;
        $this->where = $where;
        $this->joins = $joins;
        $this->order = $order;
        $this->groupBy = $group;
        $this->limit = $limit;
        return $this->buildQuery();
    }

    public function buildQuery(): string
    {
        return QueryBuilder::select(
            $this->table,
            $this->columns,
            $this->where,
            $this->joins,
            $this->groupBy,
            $this->limit
        );
    }

    public function findOneBy(string|array $criteria, string|array $field = 'id', bool $skipParse = false): mixed
    {
        $where = [];
        if(is_array($field)) {
            foreach($field as $f) {
                $where[] = "$f = ?";
            }
        } else {
            //We're building the where statement elsewhere
            if(str_contains($field, '?')) {
                $where[] = $field;
            } else {
                $where[] = ["$field = ?"];
            }
        }
        if(!is_array($criteria)) {
            $criteria = (array) $criteria;
        }
        return $this->row(QueryBuilder::select($this->table, $this->columns, $where, joins: $this->joins, limit:'0,1'), $criteria, $skipParse)->getResult();
    }

    private function processJoins(array $joins): string
    {
        if($joins) {
            $joinLine = '';
            foreach ($joins as $j) {
                $j = str_replace('LEFT JOIN ', '', $j);
                if(str_contains($j, ' JOIN')) {
                    $joinLine .= "$j\n";
                } else {
                    $joinLine .= "LEFT JOIN $j\n";
                }
            }
            $joins = $joinLine;
        } else {
            $joins = '';
        }
        return $joins;
    }

    private function processOrder(null|array $order): string
    {
        if($order) {
            $order = implode("\n AND ", $this->order);
            return $order;
        }
        return '';
    }

    private function processGroupBy(): ?string
    {
        if($this->groupBy) {
            return implode(", ", $this->groupBy);
        }
        return null;
    }

    private function setResult(mixed $data, bool $skipParse = false): static
    {
        if(!$data) {
            $this->result = false;
            return $this;
        }
        if(isset($this->entityClass) && !$skipParse) {
            $this->result = new $this->entityClass(...$this->processRow($data));
        } else {
            $this->result = $data;
        }
        return $this;
    }

    private function setResults(array $data, bool $skipParse = false): static
    {
        $this->result = [];
        if(isset($this->entityClass) && !$skipParse) {
            foreach($data as $row) {
                $this->result[] = new $this->entityClass(...$this->processRow($row));
            }
        } else {
            $this->result = $data;
        }
        return $this;
    }

    private function processRow(array $row): array
    {
        foreach($row as $k => &$d) {
            if(class_exists($this->entityMetadata[$k]->getType()->getName())) {
                $class = new ReflectionClass($this->entityMetadata[$k]->getType()->getName());
                if($class->isEnum()) {
                    $d = $this->castToEnum($class->getName(), $d);
                } else {
                    if($this->entityMetadata[$k]->getType()->allowsNull() && !$d) {
                        $d = $d;
                    } else {
                        $d = new($class->getName())($d);
                    }
                }
            }
        }
        return $row;
    }

    private function fetchEntityMetadata(): static
    {
        if($this->entityClass) {
            $properties = new ReflectionClass($this->entityClass);
            foreach($properties->getProperties() as $p) {
                $this->entityMetadata[$p->getName()] = $p;
            }
        }
        return $this;
    }

    private function castToEnum(string $enum, string|int $value)
    {
        return call_user_func($enum."::tryFrom", $value);
    }

    public function setEntity(string $entity): static
    {
        $this->entityClass = $entity;
        $this->fetchEntityMetadata();
        return $this;
    }

    public function getResult(): mixed
    {
        return $this->result;
    }

    public function getResults(): array
    {
        return $this->result;
    }

    public function setPages(string $field = 'id', array $params = []): static
    {
        //TODO: Refactor to use buildQuery
        $query = sprintf(
            "SELECT count(%s) FROM %s %s",
            $field,
            $this->table,
            $this->where ? "WHERE ". implode("\n AND ", $this->where) : null
        );
        // var_dump($query);
        $count = $this->db->cell(
            $query,
            ...$params
        );
        $this->pages = ceil($count / self::PER_PAGE);
        return $this;
    }

    public function getPages(): int
    {
        return $this->pages;
    }

    public function getPdo(): PDO
    {
        return $this->db->getPdo();
    }

    private function logQuery(string $query, array $trace): void
    {
        $this->logger->info($query, [
            'trace' => $trace,
            'request' => $this->requestId
        ]);
    }

}
