<?php

namespace App\Common\Models\Weixin2\Authorize;

use App\Common\Models\Base\Base;

class Authorizer extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Weixin2\Mysql\Authorize\Authorizer());
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
            $options[$item['appid']] = $item['nick_name'];
        }
        return $options;
    }
}
