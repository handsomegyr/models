<?php

namespace App\Backend\Submodules\Qyweixin\Models\Msg;

class Type extends \App\Common\Models\Qyweixin\Msg\Type
{

    use \App\Backend\Models\Base;

    /**
     * 获取所有列表
     *
     * @return array
     */
    public function getAll()
    {
        $query = $this->getQuery();
        $sort = array('_id' => 1);
        $list = $this->findAll($query, $sort);
        $options = array();
        foreach ($list as $item) {
            $options[$item['value']] = $item['name'];
        }
        return $options;
    }
}
