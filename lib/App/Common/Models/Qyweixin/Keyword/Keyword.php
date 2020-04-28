<?php

namespace App\Common\Models\Qyweixin\Keyword;

use App\Common\Models\Base\Base;

class Keyword extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Qyweixin\Mysql\Keyword\Keyword());
    }

    /**
     * 所有列表
     *
     * @return array
     */
    public function getAll($field_type = '')
    {
        $query = array();
        if (!empty($field_type)) {
            $query[$field_type] = array('$ne' => '');
        }
        $list = $this->findAll($query, array('_id' => 1));
        $options = array();
        foreach ($list as $item) {
            $options[$item['_id']] = $item['keyword'];
        }
        return $options;
    }
}
