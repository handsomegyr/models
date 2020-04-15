<?php

namespace App\Weixin2\Models\AppchatMsg;

class AppchatMsg extends \App\Common\Models\Weixin2\AppchatMsg\AppchatMsg
{

    /**
     * 获取指定客服内容
     *
     * @param array $match            
     * @return array
     */
    public function getAppchatMsgsByKeyword($match)
    {
        if (!empty($match['appchat_msg_ids'])) {
            $appchat_msg_ids = implode("_", $match['appchat_msg_ids']);
            $cacheKey = "appchat_msg:appchat_msg_ids:{$appchat_msg_ids}:authorizer_appid:{$match['authorizer_appid']}:component_appid:{$match['component_appid']}:agentid:{$match['agentid']}:msg_type:{$match['appchat_msg_type']}";
            $cacheKey = cacheKey(__FILE__, __CLASS__, $cacheKey);
            $cache = $this->getDI()->get('cache');
            $rst = $cache->get($cacheKey);
            if (true || empty($rst)) {
                $rst = $this->getListByIdsAndAppchatMsgType($match['appchat_msg_ids'], $match['authorizer_appid'], $match['component_appid'], $match['agentid'], $match['appchat_msg_type']);
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

    public function getListByIdsAndAppchatMsgType($ids, $authorizer_appid, $component_appid, $agentid, $msg_type)
    {
        $ret = $this->findAll(array(
            '_id' => array('$in' => $ids),
            'agentid' => $agentid,
            'authorizer_appid' => $authorizer_appid,
            'component_appid' => $component_appid,
            'msg_type' => $msg_type
        ), array('priority' => -1, '_id' => -1));
        return $ret;
    }
}
