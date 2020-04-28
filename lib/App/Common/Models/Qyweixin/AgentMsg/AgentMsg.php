<?php

namespace App\Common\Models\Qyweixin\AgentMsg;

use App\Common\Models\Base\Base;

class AgentMsg extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Qyweixin\Mysql\AgentMsg\AgentMsg());
    }

    /**
     * 根据类型获取所有列表
     *
     * @return array
     */
    public function getAllByType($msg_type, $field = "_id")
    {
        if (!empty($msg_type)) {
            $query = array("msg_type" => $msg_type);
        } else {
            $query = array();
        }
        $list = $this->findAll($query, array('_id' => 1));
        $options = array();
        foreach ($list as $item) {
            $options[$item[$field]] = $item['name'];
        }
        return $options;
    }

    public function getUploadPath()
    {
        return trim("qyweixin/agentmsg", '/');
    }
}
