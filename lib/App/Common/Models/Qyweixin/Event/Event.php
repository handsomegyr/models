<?php

namespace App\Common\Models\Qyweixin\Event;

use App\Common\Models\Base\Base;

class Event extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Qyweixin\Mysql\Event\Event());
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
