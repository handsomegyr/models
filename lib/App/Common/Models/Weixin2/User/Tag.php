<?php

namespace App\Common\Models\Weixin2\User;

use App\Common\Models\Base\Base;

class Tag extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Weixin2\Mysql\User\Tag());
    }

    /**
     * 获取所有列表
     *
     * @return array
     */
    public function getAllByType($field = "_id")
    {
        $list = $this->findAll(array(
            'tag_id' => array(
                '$gt' => 0
            ),
        ), array('_id' => 1));
        $options = array();
        foreach ($list as $item) {
            $options[$item[$field]] = $item['name'];
        }
        return $options;
    }
}
