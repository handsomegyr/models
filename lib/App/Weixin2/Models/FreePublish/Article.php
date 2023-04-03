<?php

namespace App\Weixin2\Models\FreePublish;

class Article extends \App\Common\Models\Weixin2\FreePublish\Article
{
    /**
     * 根据文章ID获取信
     * @param string $article_id 
     * @param string $index           
     * @param string $authorizer_appid            
     * @param string $component_appid           
     */
    public function getInfoByArticleId($article_id, $index, $authorizer_appid, $component_appid)
    {
        $info = $this->findOne(array(
            'article_id' => $article_id,
            'authorizer_appid' => $authorizer_appid,
            'component_appid' => $component_appid,
            'index' => $index,
        ));
        return $info;
    }

    public function clearExist($article_id, $authorizer_appid, $component_appid, $now)
    {
        $updateData = array();
        $updateData = array('is_exist' => 0);
        $updateData['sync_time'] = \App\Common\Utils\Helper::getCurrentTime($now);
        return $this->update(array(
            'article_id' => $article_id,
            'authorizer_appid' => $authorizer_appid,
            'component_appid' => $component_appid
        ), array('$set' => $updateData));
    }

    public function syncFreePublishArticles($authorizer_appid, $component_appid, $res, $now)
    {
        // {
        //     "total_count":TOTAL_COUNT,
        //     "item_count":ITEM_COUNT,
        //     "item":[
        //         {
        //             "article_id":ARTICLE_ID,
        //             "content": {
        //                 "news_item" : [
        //                     {
        //                         "title":TITLE,
        //                         "author":AUTHOR,
        //                         "digest":DIGEST,
        //                         "content":CONTENT,
        //                         "content_source_url":CONTENT_SOURCE_URL,
        //                         "thumb_media_id":THUMB_MEDIA_ID,
        //                         "show_cover_pic":1,
        //                         "need_open_comment":0,
        //                         "only_fans_can_comment":0,
        //                         "url":URL,
        //                         "is_deleted":false
        //                     }
        //                     //多图文消息会在此处有多篇文章
        //                 ],
        //                 "create_time": 1680228816, 
        //                 "update_time": 1680228844
        //             },
        //             "update_time": UPDATE_TIME
        //         },
        //         //可能有多个图文消息item结构
        //     ]
        // }
        $item = empty($res['item']) ? array() : $res['item'];
        foreach ($item as $info) {
            $article_id = $info['article_id'];
            $update_time = $info['update_time'];
            $this->clearExist($article_id, $authorizer_appid, $component_appid, $now);
            foreach ($info['content']['news_item'] as $index => $articleInfo) {
                $this->saveInfo(
                    $article_id,
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

    public function syncFreePublishArticle($article_id, $authorizer_appid, $component_appid, $res, $now)
    {
        // {
        //     "news_item": [
        //         {
        //             "title":TITLE,
        //             "author":AUTHOR,
        //             "digest":DIGEST,
        //             "content":CONTENT,
        //             "content_source_url":CONTENT_SOURCE_URL,
        //             "thumb_media_id":THUMB_MEDIA_ID,
        //             "show_cover_pic":1,
        //             "need_open_comment":0,
        //             "only_fans_can_comment":0,
        //             "url":URL,
        //             "is_deleted":false
        //         }
        //         //多图文消息应有多段 news_item 结构
        //     ],
        //     "create_time": 1680228816, 
        //     "update_time": 1680228844
        // }
        $this->clearExist($article_id, $authorizer_appid, $component_appid, $now);
        foreach ($res['news_item'] as $index => $articleInfo) {
            $this->saveInfo(
                $article_id,
                $index,
                $authorizer_appid,
                $component_appid,
                $articleInfo,
                $now,
                $res['create_time'],
                $res['update_time']
            );
        }
    }

    private function saveInfo($article_id, $index, $authorizer_appid, $component_appid, $articleInfo, $now, $create_time, $update_time)
    {
        $info = $this->getInfoByArticleId($article_id, $index, $authorizer_appid, $component_appid);
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
        $data['is_deleted'] = $articleInfo['is_deleted'];
        $data['create_time'] = \App\Common\Utils\Helper::getCurrentTime($create_time);
        $data['update_time'] = \App\Common\Utils\Helper::getCurrentTime($update_time);
        $data['is_exist'] = 1;
        $data['sync_time'] = \App\Common\Utils\Helper::getCurrentTime($now);
        if (!empty($info)) {
            return $this->update(array('_id' => $info['_id']), array('$set' => $data));
        } else {
            $data['authorizer_appid'] = $authorizer_appid;
            $data['component_appid'] = $component_appid;
            $data['article_id'] = $article_id;
            $data['index'] = $index;
            $this->insert($data);
        }
    }
}
