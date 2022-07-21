<?php

namespace App\Backend\Submodules\Qyweixin\Models\ExternalContact;

class InterceptRule extends \App\Common\Models\Qyweixin\ExternalContact\InterceptRule
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
            $options[$item['rule_id']] = $item['rule_name'];
        }
        return $options;
    }
}
