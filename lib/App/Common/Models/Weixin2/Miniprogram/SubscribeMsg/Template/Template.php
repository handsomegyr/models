<?php

namespace App\Common\Models\Weixin2\Miniprogram\SubscribeMsg\Template;

use App\Common\Models\Base\Base;

class Template extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Weixin2\Mysql\Miniprogram\SubscribeMsg\Template\Template());
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
            $options[$item['template_id']] = $item['title'];
        }
        return $options;
    }
}
