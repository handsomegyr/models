<?php

namespace App\Weixin2\Models\ReplyMsg;

class News extends \App\Common\Models\Weixin2\ReplyMsg\News
{

    public function getListByReplyMsgId($reply_msg_id)
    {
        $ret = $this->findAll(array(
            'reply_msg_id' => $reply_msg_id
        ), array('index' => 1, '_id' => -1));
        return $ret;
    }

    public function getArticlesByReplyMsgId($reply_msg_id, $msg_type, $isFirst = true)
    {
        $articles = array();
        $cacheKey = "replymsgnews:reply_msg_id:{$reply_msg_id}:msg_type:{$msg_type}";
        $cacheKey = \App\Common\Utils\Helper::myCacheKey(__CLASS__, $cacheKey);
        $cache = $this->getDI()->get('cache');
        $articles = $cache->get($cacheKey);
        if (true || empty($articles)) {
            $rst = $this->getListByReplyMsgId($reply_msg_id);
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
                        'picurl' => $isFirst ? (empty($row['index']) ? $this->getPhysicalFilePath($row['big_pic_url'])  : $this->getPhysicalFilePath($row['small_pic_url'])) : $this->getPhysicalFilePath($row['small_pic_url']),
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
