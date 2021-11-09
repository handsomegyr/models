<?php

namespace App\Backend\Submodules\Qyweixin\Models\Agent;

class Agent extends \App\Common\Models\Qyweixin\Agent\Agent
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
        $sort = array('agentid' => 1);
        $list = $this->findAll($query, $sort);
        $options = array();
        foreach ($list as $item) {
            $options[$item['agentid']] = $item['name'];
        }
        return $options;
    }
}
