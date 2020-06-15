<?php

namespace App\Database\Models\Plugin\Collection;

class Structure extends \App\Common\Models\Database\Plugin\Collection\Structure
{
    /**
     * 根据字段名获取信息
     */
    public function getInfoByField($field, $plugin_collection_id, $plugin_id)
    {
        $query = array(
            'plugin_id' => $plugin_id,
            'plugin_collection_id' => $plugin_collection_id,
            'field' => $field,
        );
        $info = $this->findOne($query);
        return $info;
    }
}
