<?php

namespace App\Qyweixin\Models\Keyword;

class KeywordToService extends \App\Common\Models\Qyweixin\Keyword\KeywordToService
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
