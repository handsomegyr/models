<?php

namespace App\Backend\Submodules\Qyweixin\Models\AppchatMsg;

class Appchat extends \App\Common\Models\Qyweixin\AppchatMsg\Appchat
{

    use \App\Backend\Models\Base;

    /**
     * 所有列表
     *
     * @return array
     */
    public function getAll()
    {
        $query = $this->getQuery();
        $sort = array('chatid' => 1);
        $list = $this->findAll($query, $sort);

        $options = array();
        foreach ($list as $item) {
            $options[$item['chatid']] = $item['name'];
        }
        return $options;
    }
}
