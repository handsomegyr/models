<?php

namespace App\Weixin2\Models\CustomMsg;

class News extends \App\Common\Models\Weixin2\CustomMsg\News
{

    public function getListByCustomMsgId($custom_msg_id, $authorizer_appid, $component_appid, $agentid)
    {
        $ret = $this->findAll(array(
            'custom_msg_id' => $custom_msg_id,
            'agentid' => $agentid,
            'authorizer_appid' => $authorizer_appid,
            'component_appid' => $component_appid,
            'custom_msg_id' => $custom_msg_id
        ), array('index' => 1, '_id' => -1));
        return $ret;
    }

    public function getArticlesByCustomMsgId($custom_msg_id, $authorizer_appid, $component_appid, $agentid, $msg_type, $isFirst = true)
    {
        $articles = array();
        $cacheKey = "custommsgnews:custom_msg_id:{$custom_msg_id}:authorizer_appid:{$authorizer_appid}:component_appid:{$component_appid}:agentid:{$agentid}";
        $cacheKey = cacheKey(__FILE__, __CLASS__, $cacheKey);
        $cache = $this->getDI()->get('cache');
        $articles = $cache->get($cacheKey);
        if (true || empty($articles)) {
            $rst = $this->getListByCustomMsgId($custom_msg_id, $authorizer_appid, $component_appid, $agentid);
            $articles = array();
            if (!empty($rst)) {
                foreach ($rst as $row) {
                    /**
                     * Title 是 图文消息标题
                     * Description 是 图文消息描述
                     * PicUrl 是 图片链接，支持JPG、PNG格式，较好的效果为大图360*200，小图200*200
                     * Url 是 点击图文消息跳转链接
                     */
                    array_push($articles, array(
                        'title' => $row['title'],
                        'description' => $row['description'],
                        'picurl' => $isFirst ? (empty($row['index']) ? $this->getPhysicalFilePath($row['big_pic_url']) : $this->getPhysicalFilePath($row['small_pic_url'])) : $this->getPhysicalFilePath($row['small_pic_url']),
                        'url' => !empty($row['url']) ? $row['url'] : ''
                    ));
                }
            }
            if (!empty($articles)) {
                // 加缓存处理
                $expire_time = 5 * 60; // 5分钟
                $cache->save($cacheKey, $articles, $expire_time);
            }
        }
        return $articles;
    }
}
