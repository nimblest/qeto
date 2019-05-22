<?php

namespace Qeto;

class BaseQueries
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
}
