<?php

namespace App\Backend\Submodules\Weixin2\Models\Notification;

class TaskProcess extends \App\Common\Models\Weixin2\Notification\TaskProcess
{

    use \App\Backend\Models\Base;


    /**
     * 所有列表
     *
     * @return array
     */
    public static function getAll()
    {
        $list = self::orderBy('id', 'asc')
            ->select('name', 'id')
            ->get();
        $options = array();
        foreach ($list as $item) {
            $options[$item->id] = $item->name;
        }
        return $options;
    }
}
