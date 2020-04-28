<?php

namespace App\Common\Models\Qyweixin\Provider;

use App\Common\Models\Base\Base;

class Provider extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Qyweixin\Mysql\Provider\Provider());
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
