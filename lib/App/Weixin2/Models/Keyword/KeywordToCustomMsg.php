<?php

namespace App\Weixin2\Models\Keyword;

class KeywordToCustomMsg extends \App\Common\Models\Weixin2\Keyword\KeywordToCustomMsg
{

    public function getCustomMsgIdsByKeywordId($keyword_id)
    {
        $q = $this->getModel()->query();
        $q->where('keyword_id', $keyword_id);
        $list = $q->get();
        $ret = array();
        if (!empty($list)) {
            foreach ($list as $item) {
                $item = $this->getReturnData($item);
                $ret[] = $item['custom_msg_id'];
            }
        }
        return $ret;
    }

    public function getKeywordIdsByCustomMsgId($custom_msg_id)
    {
        $q = $this->getModel()->query();
        $q->where('custom_msg_id', $custom_msg_id);
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
