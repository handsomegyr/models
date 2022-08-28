<?php

namespace App\Backend\Submodules\Qyweixin\Models\Auth;

class Corp extends \App\Common\Models\Qyweixin\Auth\Corp
{

    use \App\Backend\Models\Base;

    /**
     * 获取所有列表
     *
     * @return array
     */
    public function getAll($provider_appid = 0, $authorizer_appid = 0)
    {
        $query = $this->getQuery();
        $sort = array('_id' => 1);
        $list = $this->findAll($query, $sort);
        $options = array();
        foreach ($list as $item) {
            $options[$item['corpid']] = $item['corpid'];
        }
        return $options;
    }
}
