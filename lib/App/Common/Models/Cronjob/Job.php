<?php

namespace App\Common\Models\Cronjob;

use App\Common\Models\Base\Base;

class Job extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Cronjob\Mysql\Job());
    }

    /**
     * 获取所有列表
     *
     * @return array
     */
    public function getAll()
    {
        $list = $this->findAll(array(), array('_id' => 1));
        $options = array();
        foreach ($list as $item) {
            $options[$item['_id']] = $item['name'];
        }
        return $options;
    }
}
