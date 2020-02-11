<?php

namespace App\Weixin2\Models\TemplateMsg;

use Cache;

class TemplateMsg extends \App\Common\Models\Weixin2\TemplateMsg\TemplateMsg
{

    /**
     * 获取指定模板内容
     *
     * @param array $match            
     * @return array
     */
    public function getTemplateMsgsByKeyword($match)
    {
        if (!empty($match['template_msg_ids'])) {
            $template_msg_ids = implode("_", $match['template_msg_ids']);
            $cacheKey = "template_msg:template_msg_ids:{$template_msg_ids}:authorizer_appid:{$match['authorizer_appid']}:component_appid:{$match['component_appid']}";
            if (true || !Cache::tags($this->cache_tag)->has($cacheKey)) {
                $rst = $this->getListByIdsAndTemplateMsgType($match['template_msg_ids'], $match['authorizer_appid'], $match['component_appid']);
                if (!empty($rst)) {
                    // 加缓存处理
                    $expire_time = 5 * 60;
                    Cache::tags($this->cache_tag)->put($cacheKey, $rst, $expire_time);
                }
            } else {
                $rst = Cache::tags($this->cache_tag)->get($cacheKey);
            }
            return $rst;
        } else {
            return false;
        }
    }

    public function getListByIdsAndTemplateMsgType($ids, $authorizer_appid, $component_appid)
    {
        $q = $this->getModel()->query();
        $q->whereIn('id', $ids);
        $q->where('authorizer_appid', $authorizer_appid);
        $q->where('component_appid', $component_appid);
        $q->orderby("priority", "desc")->orderby("id", "desc");

        $list = $q->get();
        $ret = array();
        if (!empty($list)) {
            foreach ($list as $item) {
                $item = $this->getReturnData($item);
                $ret[] = $item;
            }
        }
        return $ret;
    }
}
