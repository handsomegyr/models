<?php

namespace App\Backend\Submodules\Qyweixin\Models\ExternalContact;

class MsgTemplate extends \App\Common\Models\Qyweixin\ExternalContact\MsgTemplate
{

    use \App\Backend\Models\Base;

    /**
     * 根据类型获取所有列表
     *
     * @return array
     */
    public function getAll($field = "_id")
    {
        $query = $this->getQuery();
        $sort = array('_id' => 1);
        $list = $this->findAll($query, $sort);
        $options = array();
        foreach ($list as $item) {
            $options[$item[$field]] = $item['name'];
        }
        return $options;
    }
}
