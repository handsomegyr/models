<?php

namespace App\Weixin2\Models\Material;

class News extends \App\Common\Models\Weixin2\Material\News
{

    public function getListByMaterialId($material_id, $authorizer_appid, $component_appid)
    {
        $ret = $this->findAll(array(
            'material_id' => $material_id,
            'authorizer_appid' => $authorizer_appid,
            'component_appid' => $component_appid
        ), array('index' => 1, '_id' => -1));
        return $ret;
    }

    public function getArticlesByMaterialId($material_id, $authorizer_appid, $component_appid)
    {
        $articles = array();
        $cacheKey = "materialnews:material_id:{$material_id}:authorizer_appid:{$authorizer_appid}:component_appid:{$component_appid}";
        $cacheKey = cacheKey(__FILE__, __CLASS__, $cacheKey);
        $cache = $this->getDI()->get('cache');
        $articles = $cache->get($cacheKey);
        if (true || empty($articles)) {
            $rst = $this->getListByMaterialId($material_id, $authorizer_appid, $component_appid);
            $articles = array();
            if (!empty($rst)) {
                foreach ($rst as $row) {
                    /**
                     * {
                     * "articles": [{
                     * "title": TITLE,
                     * "thumb_media_id": THUMB_MEDIA_ID,
                     * "author": AUTHOR,
                     * "digest": DIGEST,
                     * "show_cover_pic": SHOW_COVER_PIC(0 / 1),
                     * "content": CONTENT,
                     * "content_source_url": CONTENT_SOURCE_URL,
                     * "need_open_comment":1,
                     * "only_fans_can_comment":1
                     * },
                     * //若新增的是多图文素材，则此处应还有几段articles结构
                     * ]
                     * }
                     */
                    $article = array();
                    $article['title'] = $row['title'];
                    $article['thumb_media_id'] = $row['thumb_media_id'];
                    if (!empty($row['author'])) {
                        $article['author'] = $row['author'];
                    }
                    if (!empty($row['digest'])) {
                        $article['digest'] = $row['digest'];
                    }
                    $article['show_cover_pic'] = empty($row['show_cover_pic']) ? 0 : 1;
                    $article['content'] = $row['content'];
                    $article['content_source_url'] = trim($row['content_source_url']);
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

    public function recordMediaId($id, $res, $now)
    {
        $updateData = array();
        $updateData['media_id'] = $res['media_id'];
        $updateData['media_time'] = \App\Common\Utils\Helper::getCurrentTime($now);
        return $this->update(array('_id' => $id), array('$set' => $updateData));
    }

    public function removeMediaId($id)
    {
        $updateData = array();
        $updateData['media_id'] = "";
        return $this->update(array('_id' => $id), array('$set' => $updateData));
    }
}
