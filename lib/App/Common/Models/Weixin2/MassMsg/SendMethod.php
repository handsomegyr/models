<?php

namespace App\Common\Models\Weixin2\MassMsg;

use App\Common\Models\Base\Base;

class SendMethod extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Weixin2\Mysql\MassMsg\SendMethod());
    }
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
