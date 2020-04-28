<?php

namespace App\Common\Models\Qyweixin\AppchatMsg;

use App\Common\Models\Base\Base;

class Appchat extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Qyweixin\Mysql\AppchatMsg\Appchat());
    }
    /**
     * 所有列表
     *
     * @return array
     */
    public function getAll()
    {
        $query = array();
        $list = $this->findAll($query, array('chatid' => 1));
        $options = array();
        foreach ($list as $item) {
            $options[$item['chatid']] = $item['name'];
        }
        return $options;
    }
}
