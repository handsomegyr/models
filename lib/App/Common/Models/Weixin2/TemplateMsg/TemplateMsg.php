<?php

namespace App\Common\Models\Weixin2\TemplateMsg;

use App\Common\Models\Base\Base;

class TemplateMsg extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Weixin2\Mysql\TemplateMsg\TemplateMsg());
    }

    /**
     * 所有列表
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
