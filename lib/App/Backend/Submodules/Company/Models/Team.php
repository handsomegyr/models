<?php

namespace App\Backend\Submodules\Company\Models;

class Team extends \App\Common\Models\Company\Team
{
    use \App\Backend\Models\Base;

    /**
     * 获取所有列表
     *
     * @return array
     */
    public function getAll()
    {
        $query = $this->getQuery();
        $sort = $this->getDefaultSort();
        $ret = $this->findAll($query, $sort);
        $list = array();
        foreach ($ret as $item) {
            $list[$item['_id']] = $item['name'];
        }
        return $list;
    }
}
