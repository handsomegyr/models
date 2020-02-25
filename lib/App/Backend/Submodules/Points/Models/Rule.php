<?php

namespace App\Backend\Submodules\Points\Models;

class Rule extends \App\Common\Models\Points\Rule
{
    use \App\Backend\Models\Base;

    /**
     * 获取所有列表
     *
     * @return array
     */
    public function getAll($field = '_id')
    {
        $query = $this->getQuery();
        $sort = $this->getDefaultSort();
        $ret = $this->findAll($query, $sort);

        $list = array();
        foreach ($ret as $item) {
            $list[$item[$field]] = $item['item'];
        }
        return $list;
    }
}
