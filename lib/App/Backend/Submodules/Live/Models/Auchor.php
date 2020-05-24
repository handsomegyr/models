<?php
namespace App\Backend\Submodules\Live\Models;

class Auchor extends \App\Common\Models\Live\Auchor
{
    use \App\Backend\Models\Base;
    
    /**
     * 获取所有授权者列表
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