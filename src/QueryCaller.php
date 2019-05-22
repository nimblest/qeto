<?php
namespace Qeto;

class QueryCaller
{
    protected $queryModel;
    protected $functionName = '';

    public function __construct($queryModel)
    {
        $this->queryModel = $queryModel;
    }

    /**
     * Calls the other functions in class and gets sql and binding to be returned
     * @param string $name
     * @param string $parameter
     * @return array
     */
    public function call(string $name, $parameter): array
    {
        $prepared = [];
        $this->functionName = $name;
        $this->queryModel = $this->getQModel($name);

        list($prepared['sql'], $prepared['bindings']) = array_pad($this->queryModel->{$this->functionName}($parameter), 2, null);

        return $prepared;
    }

    /**
     * Query Model Mutator
     * @param $queryModel
     * @return void
     */
    private function setQueryModel($queryModel): void
    {
        $this->queryModel = $queryModel;
    }

    /**
     * Gets and returns the correct Q Model to use
     * @param string $name
     */
    private function getQModel(string $name)
    {
        $name = preg_split('/(?=[A-Z])/', $name);
        if (count($name) < 2 || !$qModel = $this->checkForSubClass($name[0])) {
            return $this->queryModel;
        }
        unset($name[0]);
        $this->functionName = lcfirst(implode('', $name));
        $qModel->inverse = $this->queryModel->inverse;

        return $qModel;
    }

    /**
     * Based on camel casing takes the first word and checks for a file with the function name. If there returns the instantiated model
     * @param string $possibleName
     */
    private function checkForSubClass(string $possibleName)
    {
        $file = 'App\\Queries\\' . $this->queryModel->name . '\\' . $this->queryModel->name . ucfirst($possibleName) . 'Queries';
        if (!class_exists($file)) {
            return null;
        }

        return new $file;
    }
}
