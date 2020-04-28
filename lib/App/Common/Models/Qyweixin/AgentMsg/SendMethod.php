<?php

namespace App\Common\Models\Qyweixin\AgentMsg;

use App\Common\Models\Base\Base;

class SendMethod extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Qyweixin\Mysql\AgentMsg\SendMethod());
    }
    /**
     * 所有列表
     *
     * @return array
     */
    public function getAll()
    {
        $query = array();
        $list = $this->findAll($query, array('_id' => 1));
        $options = array();
        foreach ($list as $item) {
            $options[$item['_id']] = $item['name'];
        }
        return $options;
    }
}
