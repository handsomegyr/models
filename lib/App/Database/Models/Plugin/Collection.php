<?php

namespace App\Database\Models\Plugin;

class Collection extends \App\Common\Models\Database\Plugin\Collection
{
    // 根据alias获取信息
    public function getInfoByAlias($alias, $plugin_id)
    {
        $info = $this->findOne(array(
            'plugin_id' => $plugin_id,
            'alias' => $alias
        ));
        return $info;
    }

    public function checkPluginAliasExist($alias, $plugin_id)
    {
        // 检查插件集合中是否包含这些名称信息
        $info = $this->getInfoByAlias($alias, $plugin_id);

        if (empty($info)) {
            return false;
        }
        return true;
    }
}
