<?php

namespace App\Weixin2\Models\ReplyMsg;

use Cache;

class ReplyMsg extends \App\Common\Models\Weixin2\ReplyMsg\ReplyMsg
{

    /**
     * 获取指定回复内容的回复内容
     *
     * @param array $match            
     * @return array
     */
    public function getReplyMsgsByKeyword($match)
    {
        if (!empty($match['reply_msg_ids'])) {
            $reply_msg_ids = implode("_", $match['reply_msg_ids']);
            $cacheKey = "reply_msg:reply_msg_ids:{$reply_msg_ids}:authorizer_appid:{$match['authorizer_appid']}:component_appid:{$match['component_appid']}:msg_type:{$match['reply_msg_type']}";
            if (true || !Cache::tags($this->cache_tag)->has($cacheKey)) {
                $rst = $this->getListByIdsAndReplyMsgType($match['reply_msg_ids'], $match['authorizer_appid'], $match['component_appid'], $match['reply_msg_type']);
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

    public function getListByIdsAndReplyMsgType($ids, $authorizer_appid, $component_appid, $msg_type)
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
