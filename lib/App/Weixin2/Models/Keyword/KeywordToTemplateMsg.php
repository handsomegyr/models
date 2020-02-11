<?php

namespace App\Weixin2\Models\Keyword;


class KeywordToTemplateMsg extends \App\Common\Models\Weixin2\Keyword\KeywordToTemplateMsg
{

    public function getTemplateMsgIdsByKeywordId($keyword_id)
    {
        $q = $this->getModel()->query();
        $q->where('keyword_id', $keyword_id);
        $list = $q->get();
        $ret = array();
        if (!empty($list)) {
            foreach ($list as $item) {
                $item = $this->getReturnData($item);
                $ret[] = $item['template_msg_id'];
            }
        }
        return $ret;
    }

    public function getKeywordIdsByTemplateMsgId($template_msg_id)
    {
        $q = $this->getModel()->query();
        $q->where('template_msg_id', $template_msg_id);
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
