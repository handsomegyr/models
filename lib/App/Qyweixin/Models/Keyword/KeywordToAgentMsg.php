<?php

namespace App\Qyweixin\Models\Keyword;

class KeywordToAgentMsg extends \App\Common\Models\Qyweixin\Keyword\KeywordToAgentMsg
{

    public function getAgentMsgIdsByKeywordId($keyword_id)
    {
        $list = $this->findAll(array(
            'keyword_id' => $keyword_id
        ));
        $ret = array();
        if (!empty($list)) {
            foreach ($list as $item) {
                $ret[] = $item['agent_msg_id'];
            }
        }
        return $ret;
    }

    public function getKeywordIdsByAgentMsgId($agent_msg_id)
    {
        $list = $this->findAll(array(
            'agent_msg_id' => $agent_msg_id
        ));
        $ret = array();
        if (!empty($list)) {
            foreach ($list as $item) {
                $ret[] = $item['keyword_id'];
            }
        }
        return $ret;
    }
}
