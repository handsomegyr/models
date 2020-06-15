<?php

namespace App\Database\Models;

class Plugin extends \App\Common\Models\Database\Plugin
{
    /**
     * 检查插件是否存在
     *
     * @param string $name            
     * @return bool True/False
     */
    public function checkPluginNameExist($name)
    {
        $check = $this->findOne(array(
            'name' => $name
        ));
        if (!empty($check)) {
            return true;
        }
        return false;
    }
}
