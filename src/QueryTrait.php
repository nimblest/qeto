<?php

namespace Qeto;

use Qeto\QueryCaller;

trait QueryTrait
{
    /**
     * Returns the raw sql string based on the query model for the name provided
     * @param string $name
     * @param string|string $parameter
     * @return array
     */
    public static function qWhereRaw(string $name, string $parameter = ''): array
    {
        $queryModel = self::getQueryGenerator();
        return (new QueryCaller($queryModel))->call($name, $parameter);
    }

    /**
     * Returns the raw sql string based on the query models inverse for the name provided
     * @param string $name
     * @param string|string $parameter
     * @return string
     */
    public static function qIWhereRaw(string $name, string $parameter = ''): array
    {
        $queryModel = self::getQueryGenerator();
        $queryModel->isInverse(true);
        return (new QueryCaller($queryModel))->call($name, $parameter);
    }


    /**
     * Laravel scope that can be used to implement the Qeto query
     * @param type $query
     * @param string $name
     * @param string|string $parameter
     * @return type
     */
    public function scopeQWhere($query, string $name, string $parameter = '')
    {
        $parameters = self::qWhereRaw($name, $parameter);
        return $query->whereRaw($parameters['string'], $parameters['bindings']);
    }

    /**
     * Laravel scope that can be used to implement the Qeto inverse query
     * @param type $query
     * @param string $name
     * @param string|string $parameter
     * @return type
     */
    public function scopeQIWhere($query, string $name, string $parameter = '')
    {
        $parameters = self::qWhereRawInverse($name, $parameter);
        return $query->whereRaw($parameters['string'], $parameters['bindings']);
    }

    /**
     * Returns the relation query
     * @param string $name
     * @param string|string $parameter
     * @return array
     */
    public static function qRelationWhereRaw(string $relation, string $name, string $parameter = ''): array
    {
        $queryModel = self::getQueryGenerator($relation);
        return (new QueryCaller($queryModel))->call($name, $parameter);
    }

    /**
     * Returns the relation query for the inverse
     * @param string $name
     * @param string|string $parameter
     * @return string
     */
    public static function qIRelationWhereRaw(string $relation, string $name, string $parameter = ''): array
    {
        $queryModel = self::getQueryGenerator();
        $queryModel->isInverse(true);
        return (new QueryCaller($queryModel))->call($name, $parameter);
    }

    /**
     * Gets the query model based on the facade that you have called it on.
     */
    protected static function getQueryGenerator($relation = '')
    {
        $namespace = explode('\\', get_called_class());
        $modelName = $relation ? ucfirst($relation) : end($namespace);
        $queryName = '\\App\\Queries\\' . $modelName . 'Queries';
        return new $queryName;
    }
}
