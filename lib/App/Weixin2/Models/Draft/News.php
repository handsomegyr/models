<?php

namespace App\Weixin2\Models\Draft;

class News extends \App\Common\Models\Weixin2\Draft\News
{
    public function getListByDraftId($draft_id)
    {
        $ret = $this->findAll(array(
            'draft_id' => $draft_id
        ), array('index' => 1, '_id' => -1));
        return $ret;
    }

    public function getArticlesByDraftId($draft_id)
    {
        $articles = array();
        $cacheKey = "draftnews:draft_id:{$draft_id}";
        $cacheKey = \App\Common\Utils\Helper::myCacheKey(__CLASS__, $cacheKey);
        $cache = $this->getDI()->get('cache');
        $articles = $cache->get($cacheKey);
        if (true || empty($articles)) {
            $rst = $this->getListByDraftId($draft_id);
            $articles = array();
            if (!empty($rst)) {
                foreach ($rst as $row) {
                    /**
                     *{
                     *"articles": [
                     *    {
                     *        "title":TITLE,
                     *        "author":AUTHOR,
                     *        "digest":DIGEST,
                     *        "content":CONTENT,
                     *        "content_source_url":CONTENT_SOURCE_URL,
                     *        "thumb_media_id":THUMB_MEDIA_ID,
                     *        "need_open_comment":0,
                     *        "only_fans_can_comment":0
                     *    }
                     *    //若新增的是多图文素材，则此处应还有几段articles结构
                     *]}
                     */
                    $article = array();
                    $article['title'] = $row['title'];
                    if (!empty($row['author'])) {
                        $article['author'] = $row['author'];
                    }
                    if (!empty($row['digest'])) {
                        $article['digest'] = $row['digest'];
                    }
                    $article['content'] = $row['content'];
                    $article['content_source_url'] = trim($row['content_source_url']);

                    $article['thumb_media'] = $row['thumb_media'];
                    $article['thumb_media_id'] = $row['thumb_media_id'];

                    $article['show_cover_pic'] = empty($row['show_cover_pic']) ? 0 : 1;
                    $article['need_open_comment'] = empty($row['need_open_comment']) ? 0 : 1;
                    $article['only_fans_can_comment'] = empty($row['only_fans_can_comment']) ? 0 : 1;
                    $articles[] = $article;
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
