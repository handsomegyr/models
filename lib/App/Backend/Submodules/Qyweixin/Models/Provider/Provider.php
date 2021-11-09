<?php

namespace App\Backend\Submodules\Qyweixin\Models\Provider;

class Provider extends \App\Common\Models\Qyweixin\Provider\Provider
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
        $sort = array('appid' => 1);
        $list = $this->findAll($query, $sort);
        $options = array();
        foreach ($list as $item) {
            $options[$item['appid']] = $item['name'];
        }
        return $options;
    }
}
