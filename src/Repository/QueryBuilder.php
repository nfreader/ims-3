<?php

namespace App\Repository;

class QueryBuilder
{
    public static function select(
        string $table,
        array $columns,
        ?array $where = null,
        ?array $joins = null,
        ?array $group = null,
        ?array $orderBy = null, //['field_name' => 'DESC', 'field_name' => 'ASC']
        ?string $limit = '0,100'
    ) {

        $where = self::buildWhere($where);
        $joins = self::buildJoins($joins);
        $group = self::buildGroupBy($group);
        $orderBy = self::buildOrderBy($orderBy);

        return sprintf(
            "SELECT %s FROM %s %s %s %s %s",
            implode(",\n", $columns),
            $table,
            $joins ?: null,
            $where ? "WHERE $where" : null,
            $group ? "GROUP BY $group" : null,
            $orderBy ? "ORDER BY $orderBy" : null,
            $limit ? "LIMIT $limit" : null,
        );

    }

    public static function buildWhere(?array $where): ?string
    {
        if(is_array($where)) {
            return implode("\n AND ", $where);
        } elseif (str_contains($where, 'AND')) {
            return $where;
        }
        return null;
    }

    public static function buildJoins(?array $joins): ?string
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
            return null;
        }
        return $joins;
    }

    public static function buildGroupBy(?array $group = null): ?string
    {
        if($group) {
            return implode(", ", $group);
        }
        return null;
    }

    public static function buildOrderBy(?array $orderBy = null): ?string
    {
        if($orderBy) {
            $order = [];
            foreach($orderBy as $field => $dir) {
                $dir = strtoupper($dir);
                $order[] = "$field $dir";
            }
            return implode(", ", $order);
        }

        return null;
    }

}
