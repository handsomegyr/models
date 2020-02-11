<?php

namespace App\Weixin2\Models\Keyword;

class KeywordToReplyMsg extends \App\Common\Models\Weixin2\Keyword\KeywordToReplyMsg
{

    public function getReplyMsgIdsByKeywordId($keyword_id)
    {
        $q = $this->getModel()->query();
        $q->where('keyword_id', $keyword_id);
        $list = $q->get();
        $ret = array();
        if (!empty($list)) {
            foreach ($list as $item) {
                $item = $this->getReturnData($item);
                $ret[] = $item['reply_msg_id'];
            }
        }
        return $ret;
    }

    public function getKeywordIdsByReplyMsgId($reply_msg_id)
    {
        $q = $this->getModel()->query();
        $q->where('reply_msg_id', $reply_msg_id);
        $list = $q->get();
        $ret = array();
        if (!empty($list)) {
            foreach ($list as $item) {
                $item = $this->getReturnData($item);
                $ret[] = $item['keyword_id'];
            }
        }
        return $ret;
    }
}
