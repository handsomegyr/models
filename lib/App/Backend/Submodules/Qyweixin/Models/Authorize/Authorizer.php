<?php

namespace App\Backend\Submodules\Qyweixin\Models\Authorize;

class Authorizer extends \App\Common\Models\Qyweixin\Authorize\Authorizer
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
            $options[$item['appid']] = $item['nick_name'];
        }
        return $options;
    }
}
