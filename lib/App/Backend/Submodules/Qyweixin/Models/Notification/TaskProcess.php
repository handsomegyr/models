<?php

namespace App\Backend\Submodules\Qyweixin\Models\Notification;

class TaskProcess extends \App\Common\Models\Qyweixin\Notification\TaskProcess
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
        $sort = array('_id' => 1);
        $list = $this->findAll($query, $sort);
        $options = array();
        foreach ($list as $item) {
            $options[$item['_id']] = $item['name'];
        }
        return $options;
    }
}
