<?php

namespace App\Database\Models;

class Setting extends \App\Common\Models\Database\Setting
{

    /**
     * 从系统集合中获取全局的配置参数
     *
     * @return array
     */
    public function getSetting()
    {
        $setting = array();
        $list = $this->findAll(array());
        if (!empty($list)) {
            foreach ($list as $key => $row) {
                $setting[$row['key']] = $row['value'];
            }
        }
        return $setting;
    }
}
