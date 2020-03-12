<?php

namespace App\Weixin2\Models\Keyword;

class KeywordToReplyMsg extends \App\Common\Models\Weixin2\Keyword\KeywordToReplyMsg
{

    public function getReplyMsgIdsByKeywordId($keyword_id)
    {
        $list = $this->findAll(array(
            'keyword_id' => $keyword_id
        ));

        $ret = array();
        if (!empty($list)) {
            foreach ($list as $item) {
                $ret[] = $item['reply_msg_id'];
            }
        }
        return $ret;
    }

    public function getKeywordIdsByReplyMsgId($reply_msg_id)
    {
        $list = $this->findAll(array(
            'reply_msg_id' => $reply_msg_id
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
