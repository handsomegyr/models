<?php

namespace App\Database\Models\Plugin\Collection;

class Data extends \App\Common\Models\Database\Plugin\Collection\Data
{
    public function getInfoByPluginCollectionId($plugin_id, $plugin_collection_id)
    {
        $info = $this->findOne(array(
            'plugin_id' => $plugin_id,
            'plugin_collection_id' => $plugin_collection_id
        ));
        return $info;
    }

    /**
     * 检测当前$collection_id是否为默认数据集合
     *
     * @param string $collection_id      
     */
    public function isDefault($data_collection_id)
    {
        $check = $this->findOne(array(
            'data_collection_id' => $data_collection_id
        ));
        if (!empty($check)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 设定插件集合的默认数据
     *
     * @param string $plugin_id
     * @param string $plugin_collection_id
     * @param string $data_collection_id
     */
    public function setDefault($plugin_id, $plugin_collection_id, $data_collection_id)
    {
        $info = $this->getInfoByPluginCollectionId($plugin_id, $plugin_collection_id);
        if (empty($info)) {
            $data = array();
            $data['plugin_id'] = $plugin_id;
            $data['plugin_collection_id'] = $plugin_collection_id;
            $data['data_collection_id'] = $data_collection_id;
            $this->insert($data);
        } else {
            $this->update(array(
                '_id' => $info['_id']
            ), array(
                '$set' => array(
                    'data_collection_id' => $data_collection_id
                )
            ));
        }
    }

    /**
     * 取消默认设置
     *
     * @param string $plugin_id  
     * @param string $plugin_collection_id          
     */
    public function cancelDefault($plugin_id, $plugin_collection_id)
    {
        $this->physicalRemove(array(
            'plugin_id' => $plugin_id,
            'plugin_collection_id' => $plugin_collection_id
        ));
    }
}
