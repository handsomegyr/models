<?php

namespace App\Common\Models\Qyweixin\Attachment;

use App\Common\Models\Base\Base;

class Attachment extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Qyweixin\Mysql\Attachment\Attachment());
    }

    /**
     * 根据类型获取所有列表
     *
     * @return array
     */
    public function getAllByType($type, $field = "_id")
    {
        if (!empty($type)) {
            $query = array('type' => $type);
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
        return trim("qyweixin/attachment", '/');
    }
}
