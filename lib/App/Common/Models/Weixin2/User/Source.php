<?php

namespace App\Common\Models\Weixin2\User;

use App\Common\Models\Base\Base;

class Source extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Weixin2\Mysql\User\Source());
    }

    /**
     * 获取所有列表
     *
     * @return array
     */
    public function getAll()
    {
        $list = $this->findAll(array(), array('_id' => 1));
        $options = array();
        foreach ($list as $item) {
            $options[$item['value']] = "{$item['value']}:{$item['name']}";
        }
        return $options;
    }
}
