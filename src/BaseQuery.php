<?php

namespace Qeto;

class BaseQuery
{
    public $name;
    public $inverse = false;

    /**
     * Runs the sql query opposite
     * @param bool $inverse
     * @return void
     */
    public function isInverse(bool $inverse): void
    {
        $this->inverse = $inverse;
    }

    /**
     * Joins queries by an and
     * @param array $queries
     * @return array
     */
    public function joinByAnd(array $queries, array $parameters = []): array
    {
        $sql = '';
        $bindings = [];
        $count = 0;

        foreach ($queries as $query) {
            $param = $this->methodParams($query, $parameters);
            $qInfo = $this->{$query}($param);
            $sql .= ($count ? ' AND ' : '') . $qInfo[0];
            $bindings = array_merge($bindings, $qInfo[1]);
            $count++;
        }

        return [$sql, $bindings];
    }

    /**
     * Queries made into an orWhere
     * @param array $queries
     * @return array
     */
    public function orWhereQueries(array $queries, array $parameters = []): array
    {
        $sql = '';
        $bindings = [];
        $count = 0;
        
        foreach ($queries as $query) {
            $param = $this->methodParams($query, $parameters);
            $qInfo = $this->{$query}($param);
            $sql .= ($count ? ' OR (' : '(') . "$qInfo[0])" ;
            $bindings = array_merge($bindings, $qInfo[1]);
            $count++;
        }

        return [$sql, $bindings];
    }

    private function methodParams(string $method, array $parameters)
    {
        if (!$parameters) {
            return null;
        }
        $r = new \ReflectionMethod(get_class($this), $method);
        $count = $r->getNumberOfRequiredParameters();

        if (!$count) {
            return null;
        }

        $params = array_slice($parameters, 0, $count);

        if (count($params < 2)) {
            return $params[0];
        }
        return $params;
    }
}
