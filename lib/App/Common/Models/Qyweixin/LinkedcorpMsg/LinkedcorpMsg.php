<?php

namespace App\Common\Models\Qyweixin\LinkedcorpMsg;

use App\Common\Models\Base\Base;

class LinkedcorpMsg extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Qyweixin\Mysql\LinkedcorpMsg\LinkedcorpMsg());
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
        return trim("qyweixin/linkedcorpmsg", '/');
    }
}
