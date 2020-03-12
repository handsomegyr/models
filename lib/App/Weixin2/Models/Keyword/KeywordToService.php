<?php

namespace App\Weixin2\Models\Keyword;

class KeywordToService extends \App\Common\Models\Weixin2\Keyword\KeywordToService
{

    public function getServiceIdsByKeywordId($keyword_id)
    {
        $list = $this->findAll(array(
            'keyword_id' => $keyword_id
        ));
        $ret = array();
        if (!empty($list)) {
            foreach ($list as $item) {
                $ret[] = $item['service_id'];
            }
        }
        return $ret;
    }

    public function getKeywordIdsByServiceId($service_id)
    {
        $list = $this->findAll(array(
            'service_id' => $service_id
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
