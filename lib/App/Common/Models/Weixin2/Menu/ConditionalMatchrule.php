<?php

namespace App\Common\Models\Weixin2\Menu;

use App\Common\Models\Base\Base;

class ConditionalMatchrule extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Weixin2\Mysql\Menu\ConditionalMatchrule());
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
            $options[$item['_id']] = $item['matchrule_name'];
        }
        return $options;
    }
}
