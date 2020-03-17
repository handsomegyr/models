<?php

namespace App\Common\Models\Weixin2\ReplyMsg;

use App\Common\Models\Base\Base;

class ReplyMsg extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Weixin2\Mysql\ReplyMsg\ReplyMsg());
    }

    /**
     * 根据类型获取所有列表
     *
     * @return array
     */
    public function getAllByType($msg_type, $field = "_id")
    {
        if (!empty($msg_type)) {
            $query = array('msg_type' => $msg_type);
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
        return trim("weixin2/replymsg", '/');
    }
}
