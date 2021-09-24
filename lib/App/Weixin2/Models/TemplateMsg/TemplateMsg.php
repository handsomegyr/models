<?php

namespace App\Weixin2\Models\TemplateMsg;

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
            $cacheKey = "template_msg:template_msg_ids:{$template_msg_ids}:authorizer_appid:{$match['authorizer_appid']}:component_appid:{$match['component_appid']}}";
            $cacheKey = cacheKey(__CLASS__, $cacheKey);
            $cache = $this->getDI()->get('cache');
            $rst = $cache->get($cacheKey);
            if (true || empty($rst)) {
                $rst = $this->getListByIdsAndTemplateMsgType($match['template_msg_ids'], $match['authorizer_appid'], $match['component_appid']);
                if (!empty($rst)) {
                    // 加缓存处理
                    $expire_time = 5 * 60;
                    $cache->save($cacheKey, $rst, $expire_time);
                }
            }
            return $rst;
        } else {
            return false;
        }
    }

    public function getListByIdsAndTemplateMsgType($ids, $authorizer_appid, $component_appid)
    {
        $ret = $this->findAll(array(
            '_id' => array('$in' => $ids),
            'authorizer_appid' => $authorizer_appid,
            'component_appid' => $component_appid
        ), array('priority' => -1, '_id' => -1));
        return $ret;
    }
}
