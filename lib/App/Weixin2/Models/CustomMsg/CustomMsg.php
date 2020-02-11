<?php

namespace App\Weixin2\Models\CustomMsg;

use Cache;

class CustomMsg extends \App\Common\Models\Weixin2\CustomMsg\CustomMsg
{

    /**
     * 获取指定客服内容
     *
     * @param array $match            
     * @return array
     */
    public function getCustomMsgsByKeyword($match)
    {
        if (!empty($match['custom_msg_ids'])) {
            $custom_msg_ids = implode("_", $match['custom_msg_ids']);
            $cacheKey = "custom_msg:custom_msg_ids:{$custom_msg_ids}:authorizer_appid:{$match['authorizer_appid']}:component_appid:{$match['component_appid']}:msg_type:{$match['custom_msg_type']}";
            if (true || !Cache::tags($this->cache_tag)->has($cacheKey)) {
                $rst = $this->getListByIdsAndCustomMsgType($match['custom_msg_ids'], $match['authorizer_appid'], $match['component_appid'], $match['custom_msg_type']);
                if (!empty($rst)) {
                    // 加缓存处理
                    $expire_time = 5 * 60;
                    Cache::tags($this->cache_tag)->put($cacheKey, $rst, $expire_time);
                }
            } else {
                $rst = Cache::tags($this->cache_tag)->get($cacheKey);
            }
            return $rst;
        } else {
            return false;
        }
    }

    public function getListByIdsAndCustomMsgType($ids, $authorizer_appid, $component_appid, $msg_type)
    {
        $q = $this->getModel()->query();
        $q->whereIn('id', $ids);
        $q->where('authorizer_appid', $authorizer_appid);
        $q->where('component_appid', $component_appid);
        $q->where('msg_type', $msg_type);
        $q->orderby("priority", "desc")->orderby("id", "desc");

        $list = $q->get();
        $ret = array();
        if (!empty($list)) {
            foreach ($list as $item) {
                $item = $this->getReturnData($item);
                $ret[] = $item;
            }
        }
        return $ret;
    }
}
