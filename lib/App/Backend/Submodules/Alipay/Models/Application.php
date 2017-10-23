<?php
namespace App\Backend\Submodules\Alipay\Models;

class Application extends \App\Common\Models\Alipay\Application
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
            $list[$item['app_id']] = $item['app_name'];
        }
        return $list;
    }
}