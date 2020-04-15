<?php

namespace App\Weixin2\Models\AgentMsg;

class News extends \App\Common\Models\Weixin2\AgentMsg\News
{

    public function getListByAgentMsgId($agent_msg_id, $authorizer_appid, $component_appid, $agentid)
    {
        $ret = $this->findAll(array(
            'agent_msg_id' => $agent_msg_id,
            'agentid' => $agentid,
            'authorizer_appid' => $authorizer_appid,
            'component_appid' => $component_appid,
            'agent_msg_id' => $agent_msg_id
        ), array('index' => 1, '_id' => -1));
        return $ret;
    }

    public function getArticlesByAgentMsgId($agent_msg_id, $authorizer_appid, $component_appid, $agentid, $msg_type, $isFirst = true)
    {
        $articles = array();
        $cacheKey = "agentmsgnews:agent_msg_id:{$agent_msg_id}:authorizer_appid:{$authorizer_appid}:component_appid:{$component_appid}:agentid:{$agentid}";
        $cacheKey = cacheKey(__FILE__, __CLASS__, $cacheKey);
        $cache = $this->getDI()->get('cache');
        $articles = $cache->get($cacheKey);
        if (true || empty($articles)) {
            $rst = $this->getListByAgentMsgId($agent_msg_id, $authorizer_appid, $component_appid, $agentid);
            $articles = array();
            if (!empty($rst)) {
                foreach ($rst as $row) {
                    if ($msg_type == 'news') {
                        /**
                         * title	是	标题，不超过128个字节，超过会自动截断（支持id转译）
                         * thumb_media_id	是	图文消息缩略图的media_id, 可以通过素材管理接口获得。此处thumb_media_id即上传接口返回的media_id
                         * author	否	图文消息的作者，不超过64个字节
                         * content_source_url	否	图文消息点击“阅读原文”之后的页面链接
                         * content	是	图文消息的内容，支持html标签，不超过666 K个字节（支持id转译）
                         * digest	否	图文消息的描述，不超过512个字节，超过会自动截断（支持id转译）
                         */
                        $article = array();
                        $article['title'] = $row['title'];
                        $article['thumb_media'] = $row['thumb_media'];
                        if (!empty($row['author'])) {
                            $article['author'] = $row['author'];
                        }
                        if (!empty($row['digest'])) {
                            $article['digest'] = $row['digest'];
                        }
                        $article['content'] = $row['description'];
                        $article['content_source_url'] = trim($row['url']);
                        $articles[] = $article;
                    } elseif ($msg_type == 'news') {
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
