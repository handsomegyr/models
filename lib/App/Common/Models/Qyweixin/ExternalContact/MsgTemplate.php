<?php

namespace App\Common\Models\Qyweixin\ExternalContact;

use App\Common\Models\Base\Base;

class MsgTemplate extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Qyweixin\Mysql\ExternalContact\MsgTemplate());
    }

    /**
     * 根据类型获取所有列表
     *
     * @return array
     */
    public function getAll($field = "_id")
    {
        $query = array();
        $list = $this->findAll($query, array('_id' => 1));
        $options = array();
        foreach ($list as $item) {
            $options[$item[$field]] = $item['name'];
        }
        return $options;
    }

    public function getUploadPath()
    {
        return trim("qyweixin/msgtemplate", '/');
    }
}
