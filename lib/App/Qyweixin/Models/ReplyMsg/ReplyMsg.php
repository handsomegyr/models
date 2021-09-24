<?php

namespace App\Qyweixin\Models\ReplyMsg;

class ReplyMsg extends \App\Common\Models\Qyweixin\ReplyMsg\ReplyMsg
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
            $cacheKey = "reply_msg:reply_msg_ids:{$reply_msg_ids}:authorizer_appid:{$match['authorizer_appid']}:provider_appid:{$match['provider_appid']}:agentid:{$match['agentid']}:msg_type:{$match['reply_msg_type']}";
            $cacheKey = cacheKey(__CLASS__, $cacheKey);
            $cache = $this->getDI()->get('cache');
            $rst = $cache->get($cacheKey);
            if (true || empty($rst)) {
                $rst = $this->getListByIdsAndReplyMsgType($match['reply_msg_ids'], $match['authorizer_appid'], $match['provider_appid'], $match['agentid'], $match['reply_msg_type']);
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

    public function getListByIdsAndReplyMsgType($ids, $authorizer_appid, $provider_appid, $agentid, $msg_type)
    {
        $ret = $this->findAll(array(
            '_id' => array('$in' => $ids),
            'agentid' => $agentid,
            'authorizer_appid' => $authorizer_appid,
            'provider_appid' => $provider_appid,
            'msg_type' => $msg_type
        ), array('priority' => -1, '_id' => -1));
        return $ret;
    }
}
