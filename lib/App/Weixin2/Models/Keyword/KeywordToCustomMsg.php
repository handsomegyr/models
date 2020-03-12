<?php

namespace App\Weixin2\Models\Keyword;

class KeywordToCustomMsg extends \App\Common\Models\Weixin2\Keyword\KeywordToCustomMsg
{

    public function getCustomMsgIdsByKeywordId($keyword_id)
    {
        $list = $this->findAll(array(
            'keyword_id' => $keyword_id
        ));
        $ret = array();
        if (!empty($list)) {
            foreach ($list as $item) {
                $ret[] = $item['custom_msg_id'];
            }
        }
        return $ret;
    }

    public function getKeywordIdsByCustomMsgId($custom_msg_id)
    {
        $list = $this->findAll(array(
            'custom_msg_id' => $custom_msg_id
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
