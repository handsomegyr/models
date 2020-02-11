<?php

namespace App\Weixin2\Models\Keyword;

class KeywordToService extends \App\Common\Models\Weixin2\Keyword\KeywordToService
{

    public function getServiceIdsByKeywordId($keyword_id)
    {
        $q = $this->getModel()->query();
        $q->where('keyword_id', $keyword_id);
        $list = $q->get();
        $ret = array();
        if (!empty($list)) {
            foreach ($list as $item) {
                $item = $this->getReturnData($item);
                $ret[] = $item['service_id'];
            }
        }
        return $ret;
    }

    public function getKeywordIdsByServiceId($service_id)
    {
        $q = $this->getModel()->query();
        $q->where('service_id', $service_id);
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
