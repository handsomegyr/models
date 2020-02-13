<?php

namespace App\Common\Models\Weixin2\ReplyMsg;

use App\Common\Models\Base\Base;

class Type extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Weixin2\Mysql\ReplyMsg\Type());
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
            $options[$item['value']] = $item['name'];
        }
        return $options;
    }
}
