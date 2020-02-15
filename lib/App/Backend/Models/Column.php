<?php

namespace App\Backend\Models;

use Closure;

class Column //extends \stdClass
{
    protected $fieldName = null;
    protected $schemas = array();
    protected $rowModel = null;
    protected $fieldValue = null;
    protected $baseUrl = null;

    public function __construct($fieldName, $fieldValue, array $schemas, array $row, $baseUrl)
    {
        $this->fieldName = $fieldName;
        $this->fieldValue = $fieldValue;
        $this->schemas = $schemas;
        $this->baseUrl = $baseUrl;
        $this->rowModel = new \stdClass();
        foreach ($row as $key => $value) {
            $this->rowModel->$key = $value;
        }
    }

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

    public function downloadable()
    {
        if (empty($this->fieldValue)) {
            return "";
        }
        $name = $this->fieldValue;
        $src = $this->getSrc();
        return <<<HTML
<a href='{$src}' download='{$name}' target='_blank' class='text-muted'>
    <i class="fa fa-download"></i> {$name}
</a>
HTML;
    }

    public function image($src, $width = 50, $height = 50)
    {
        if (empty($this->fieldValue)) {
            return "";
        }
        if (empty($src)) {
            $src = $this->getSrc();
        }
        return "<img src='{$src}' style='max-width:{$width}px;max-height:{$height}px' class='img img-thumbnail' />";
    }

    protected function getSrc()
    {
        $id = $this->fieldValue;
        $field = $this->schemas[$this->fieldName];
        $path = "";
        if (!empty($field['data'][$field['data']['type']])) {
            $fileInfo = $field['data'][$field['data']['type']];
            $path = empty($fileInfo['path']) ? '' : trim($fileInfo['path'], '/') . '/';
        }
        return $this->baseUrl . "service/file/index?upload_path=" . trim($path, '/') . "&id=" . $id;
    }
}
