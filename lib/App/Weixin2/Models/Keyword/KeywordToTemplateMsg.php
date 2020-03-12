<?php

namespace App\Weixin2\Models\Keyword;


class KeywordToTemplateMsg extends \App\Common\Models\Weixin2\Keyword\KeywordToTemplateMsg
{

    public function getTemplateMsgIdsByKeywordId($keyword_id)
    {
        $list = $this->findAll(array(
            'keyword_id' => $keyword_id
        ));
        $ret = array();
        if (!empty($list)) {
            foreach ($list as $item) {
                $ret[] = $item['template_msg_id'];
            }
        }
        return $ret;
    }

    public function getKeywordIdsByTemplateMsgId($template_msg_id)
    {
        $list = $this->findAll(array(
            'template_msg_id' => $template_msg_id
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
