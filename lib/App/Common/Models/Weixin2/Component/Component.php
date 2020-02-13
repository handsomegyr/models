<?php

namespace App\Common\Models\Weixin2\Component;

use App\Common\Models\Base\Base;

class Component extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Weixin2\Mysql\Component\Component());
    }

    /**
     * 获取所有列表
     *
     * @return array
     */
    public function getAll()
    {
        $list = $this->findAll(array(), array('appid' => 1));
        $options = array();
        foreach ($list as $item) {
            $options[$item['appid']] = $item['name'];
        }
        return $options;
    }
}
