<?php

namespace App\Backend\Submodules\Qyweixin\Models\Keyword;

class Keyword extends \App\Common\Models\Qyweixin\Keyword\Keyword
{

    use \App\Backend\Models\Base;

    /**
     * 所有列表
     *
     * @return array
     */
    public function getAll($field_type = '')
    {
        $query = $this->getQuery();
        if (!empty($field_type)) {
            $query[$field_type] = array('$ne' => '');
        }

        $sort = array('_id' => 1);
        $list = $this->findAll($query, $sort);

        $options = array();
        foreach ($list as $item) {
            $options[$item['_id']] = $item['keyword'];
        }
        return $options;
    }
}
