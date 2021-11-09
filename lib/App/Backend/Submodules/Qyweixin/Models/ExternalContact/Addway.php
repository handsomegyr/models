<?php

namespace App\Backend\Submodules\Qyweixin\Models\ExternalContact;

class Addway extends \App\Common\Models\Qyweixin\ExternalContact\Addway
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
