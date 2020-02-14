<?php

namespace App\Backend\Models;

use Closure;

class Column //extends \stdClass
{
    public function __construct(array $row)
    {
        // $this->row = $row;
        $this->rowModel = new \stdClass();
        foreach ($row as $key => $value) {
            $this->rowModel->$key = $value;
            // $this->$key = $value;
        }
    }

    protected $rowModel = null;

    /**
     * Add a display callback.
     *
     * @param Closure $callback
     */
    public function display(Closure $callback)
    {
        $callback = $callback->bindTo($this->rowModel, $this);
        $value = call_user_func_array($callback, [$this->rowModel, $this]);
        return $value;
    }

    /**
     * Set style of this column.
     *
     * @param string $style
     *
     * @return $this
     */
    public function style($style)
    {
        return $style;
    }

}
