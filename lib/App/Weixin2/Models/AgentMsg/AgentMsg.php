<?php

namespace App\Weixin2\Models\AgentMsg;

class AgentMsg extends \App\Common\Models\Weixin2\AgentMsg\AgentMsg
{

    /**
     * 获取指定客服内容
     *
     * @param array $match            
     * @return array
     */
    public function getAgentMsgsByKeyword($match)
    {
        if (!empty($match['agent_msg_ids'])) {
            $agent_msg_ids = implode("_", $match['agent_msg_ids']);
            $cacheKey = "agent_msg:agent_msg_ids:{$agent_msg_ids}:authorizer_appid:{$match['authorizer_appid']}:component_appid:{$match['component_appid']}:agentid:{$match['agentid']}:msg_type:{$match['agent_msg_type']}";
            $cacheKey = cacheKey(__FILE__, __CLASS__, $cacheKey);
            $cache = $this->getDI()->get('cache');
            $rst = $cache->get($cacheKey);
            if (true || empty($rst)) {
                $rst = $this->getListByIdsAndAgentMsgType($match['agent_msg_ids'], $match['authorizer_appid'], $match['component_appid'], $match['agentid'], $match['agent_msg_type']);
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

    public function getListByIdsAndAgentMsgType($ids, $authorizer_appid, $component_appid, $agentid, $msg_type)
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
