<?php

namespace App\Banner\Models;

class Item extends \App\Common\Models\Banner\Item
{

    /**
     * 获取前端显示的内容
     *
     * @param array $info            
     *
     * @return array
     */
    public function getFrontData(array $info)
    {
        $data = array();
        $data['banner_id'] = $info['id'];
        $data['name'] = $info['name'];
        $data['desc'] = $info['desc'];
        $data['img_url'] = $info['img_url'];
        $data['link'] = $info['link'];
        $data['banner_id'] = $info['banner_id'];
        $data['img_url2'] = $info['img_url2'];
        $data['img_url3'] = $info['img_url3'];
        return $data;
    }

    /**
     * 根据banner_id获取当前显示的banner列表
     *
     * @param string $banner_id            
     * @param number $now            
     * @return array
     */
    public function getListByBannerId($banner_id, $now)
    {
        $now = getCurrentTime($now);
        $query = array();
        $query['start_at'] = array(
            '$lte' => $now
        );
        $query['end_at'] = array(
            '$gte' => $now
        );

        $query['banner_id'] = $banner_id;
        $query['status'] = true;

        $sort = array('sort' => -1, '_id' => -1);
        $list = $this->findAll($query, $sort);
        return $list;
    }
}
