<?php

namespace App\Common\Models\Qyweixin\Agent;

use App\Common\Models\Base\Base;

class Agent extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Qyweixin\Mysql\Agent\Agent());
    }

    /**
     * 获取所有列表
     *
     * @return array
     */
    public function getAll()
    {
        $list = $this->findAll(array(), array('agentid' => 1));
        $options = array();
        foreach ($list as $item) {
            $options[$item['agentid']] = $item['name'];
        }
        return $options;
    }
}
