<?php

namespace App\Weixin2\Models\Draft;

class News extends \App\Common\Models\Weixin2\Draft\News
{
    public function getListByDraftId($draft_id, $authorizer_appid, $component_appid)
    {
        $ret = $this->findAll(array(
            'draft_id' => $draft_id,
            'authorizer_appid' => $authorizer_appid,
            'component_appid' => $component_appid,
            'is_exist' => true
        ), array('index' => 1, '_id' => -1));
        return $ret;
    }

    public function getArticlesByDraftId($draft_id, $authorizer_appid, $component_appid)
    {
        $articles = array();
        $cacheKey = "draftnews:draft_id:{$draft_id}:authorizer_appid:{$authorizer_appid}:component_appid:{$component_appid}";
        $cacheKey = \App\Common\Utils\Helper::myCacheKey(__CLASS__, $cacheKey);
        $cache = $this->getDI()->get('cache');
        $articles = $cache->get($cacheKey);
        if (true || empty($articles)) {
            $rst = $this->getListByDraftId($draft_id, $authorizer_appid, $component_appid);
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

    public function recordMediaId($draft_id, $authorizer_appid, $component_appid, $res, $now)
    {
        $updateData = array();
        $updateData['media_id'] = $res['media_id'];
        $updateData['update_time'] = \App\Common\Utils\Helper::getCurrentTime($now);
        return $this->update(array(
            'draft_id' => $draft_id,
            'authorizer_appid' => $authorizer_appid,
            'component_appid' => $component_appid
        ), array('$set' => $updateData));
    }

    public function removeMediaId($draft_id, $authorizer_appid, $component_appid)
    {
        $updateData = array();
        $updateData['media_id'] = "";
        return $this->update(array(
            'draft_id' => $draft_id,
            'authorizer_appid' => $authorizer_appid,
            'component_appid' => $component_appid
        ), array('$set' => $updateData));
    }

    /**
     * 根据素材ID获取信
     * @param string $media_id 
     * @param string $index           
     * @param string $authorizer_appid            
     * @param string $component_appid           
     */
    public function getInfoByMediaId($media_id, $index, $authorizer_appid, $component_appid)
    {
        $info = $this->findOne(array(
            'media_id' => $media_id,
            'authorizer_appid' => $authorizer_appid,
            'component_appid' => $component_appid,
            'index' => $index,
        ));
        return $info;
    }

    public function clearExist($media_id, $authorizer_appid, $component_appid, $now)
    {
        $updateData = array();
        $updateData = array('is_exist' => 0);
        $updateData['sync_time'] = \App\Common\Utils\Helper::getCurrentTime($now);
        return $this->update(array(
            'article_id' => $media_id,
            'authorizer_appid' => $authorizer_appid,
            'component_appid' => $component_appid
        ), array('$set' => $updateData));
    }

    public function syncDraftNews($authorizer_appid, $component_appid, $res, $now)
    {
        // {
        //     "total_count":TOTAL_COUNT,
        //     "item_count":ITEM_COUNT,
        //     "item":[
        //         {
        //             "media_id":MEDIA_ID,
        //             "content": {
        //                 "news_item" : [
        //                     {
        //                         "title":TITLE,
        //                         "author":AUTHOR,
        //                         "digest":DIGEST,
        //                         "content":CONTENT,
        //                         "content_source_url":CONTENT_SOURCE_URL,
        //                         "thumb_media_id":THUMB_MEDIA_ID,
        //                         "show_cover_pic":0,
        //                         "need_open_comment":0,
        //                         "only_fans_can_comment":0,
        //                         "url":URL
        //                     },
        //                     //多图文消息会在此处有多篇文章
        //                 ]
        //             },
        //             "update_time": UPDATE_TIME
        //         },
        //         //可能有多个图文消息item结构
        //     ]
        // }
        $item = empty($res['item']) ? array() : $res['item'];
        foreach ($item as $info) {
            $media_id = $info['media_id'];
            $this->clearExist($media_id, $authorizer_appid, $component_appid, $now);
            foreach ($info['content']['news_item'] as $index => $articleInfo) {
                $this->saveInfo(
                    $media_id,
                    $index,
                    $authorizer_appid,
                    $component_appid,
                    $articleInfo,
                    $now,
                    $info['content']['create_time'],
                    $info['content']['update_time']
                );
            }
        }
    }

    private function saveInfo($media_id, $index, $authorizer_appid, $component_appid, $articleInfo, $now, $create_time, $update_time)
    {
        $info = $this->getInfoByMediaId($media_id, $index, $authorizer_appid, $component_appid);
        $data = array();
        $data['title'] = $articleInfo['title'];
        $data['author'] = $articleInfo['author'];
        $data['digest'] = $articleInfo['digest'];
        $data['content'] = $articleInfo['content'];
        $data['content_source_url'] = $articleInfo['content_source_url'];
        $data['thumb_media_id'] = $articleInfo['thumb_media_id'];
        $data['show_cover_pic'] = $articleInfo['show_cover_pic'];
        $data['need_open_comment'] = $articleInfo['need_open_comment'];
        $data['only_fans_can_comment'] = $articleInfo['only_fans_can_comment'];
        $data['url'] = $articleInfo['url'];
        // $data['is_deleted'] = $articleInfo['is_deleted'];
        $data['create_time'] = \App\Common\Utils\Helper::getCurrentTime($create_time);
        $data['update_time'] = \App\Common\Utils\Helper::getCurrentTime($update_time);
        $data['is_exist'] = 1;
        $data['sync_time'] = \App\Common\Utils\Helper::getCurrentTime($now);
        if (!empty($info)) {
            return $this->update(array('_id' => $info['_id']), array('$set' => $data));
        } else {
            $data['authorizer_appid'] = $authorizer_appid;
            $data['component_appid'] = $component_appid;
            $data['media_id'] = $media_id;
            $data['index'] = $index;
            $this->insert($data);
        }
    }
}
