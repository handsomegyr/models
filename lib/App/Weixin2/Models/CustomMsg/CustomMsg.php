<?php

namespace App\Weixin2\Models\CustomMsg;



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
            $cacheKey = cacheKey(__CLASS__, $cacheKey);
            $cache = $this->getDI()->get('cache');
            $rst = $cache->get($cacheKey);
            if (true || empty($rst)) {
                $rst = $this->getListByIdsAndCustomMsgType($match['custom_msg_ids'], $match['authorizer_appid'], $match['component_appid'], $match['custom_msg_type']);
                if (!empty($rst)) {
                    // 加缓存处理
                    $expire_time = 5 * 60;
                    $cache->save($cacheKey, $rst, $expire_time);
                }
            }
            return $rst;
        } else {
            return false;
        }
    }

    public function getListByIdsAndCustomMsgType($ids, $authorizer_appid, $component_appid, $msg_type)
    {
        $ret = $this->findAll(array(
            '_id' => array('$in' => $ids),
            'authorizer_appid' => $authorizer_appid,
            'component_appid' => $component_appid,
            'msg_type' => $msg_type
        ), array('priority' => -1, '_id' => -1));
        return $ret;
    }
}
