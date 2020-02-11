<?php

namespace App\Weixin2\Models\ReplyMsg;

use Cache;

class News extends \App\Common\Models\Weixin2\ReplyMsg\News
{

    public function getListByReplyMsgId($reply_msg_id, $authorizer_appid, $component_appid)
    {
        $q = $this->getModel()->query();
        $q->where('reply_msg_id', $reply_msg_id);
        $q->where('authorizer_appid', $authorizer_appid);
        $q->where('component_appid', $component_appid);
        $q->orderby("index", "asc")->orderby("id", "desc");
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

    public function getArticlesByReplyMsgId($reply_msg_id, $authorizer_appid, $component_appid, $isFirst = true)
    {
        $articles = array();
        $cacheKey = "replymsgnews:reply_msg_id:{$reply_msg_id}:authorizer_appid:{$authorizer_appid}:component_appid:{$component_appid}";
        if (true || !Cache::tags($this->cache_tag)->has($cacheKey)) {
            $rst = $this->getListByReplyMsgId($reply_msg_id, $authorizer_appid, $component_appid);
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
                        'picurl' => $isFirst ? (empty($row['index']) ? config('oss.url') . "/" . $row['big_pic_url'] : config('oss.url') . "/" . $row['small_pic_url']) : config('oss.url') . "/" . $row['small_pic_url'],
                        'url' => !empty($row['url']) ? $row['url'] : ''
                    ));
                }
            }
            if (!empty($articles)) {
                // 加缓存处理
                $expire_time = 5 * 60; // 5分钟
                Cache::tags($this->cache_tag)->put($cacheKey, $articles, $expire_time);
            }
        } else {
            $articles = Cache::tags($this->cache_tag)->get($cacheKey);
        }
        return $articles;
    }
}
