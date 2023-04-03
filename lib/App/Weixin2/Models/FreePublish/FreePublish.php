<?php

namespace App\Weixin2\Models\FreePublish;

class FreePublish extends \App\Common\Models\Weixin2\FreePublish\FreePublish
{
    /**
     * 根据任务ID和文章ID获取信息
     *
     * @param string $publish_id  
     * @param string $article_id           
     * @param string $authorizer_appid            
     * @param string $component_appid           
     */
    public function getInfoByPublishAndArticleId($publish_id, $article_id, $authorizer_appid, $component_appid)
    {
        $info = $this->findOne(array(
            'publish_id' => $publish_id,
            'article_id' => $article_id,
            'authorizer_appid' => $authorizer_appid,
            'component_appid' => $component_appid
        ));
        return $info;
    }

    public function syncFreePublish($authorizer_appid, $component_appid, $res, $now)
    {
        // "publish_id":"100000001",
        // "publish_status":0,
        // "article_id":ARTICLE_ID,
        // "article_detail":{
        //     "count":1,
        //     "item":[
        //         {
        //             "idx":1,
        //             "article_url": ARTICLE_URL
        //         }
        //         //如果 count 大于 1，此处会有多篇文章
        //     ]
        // },
        // "fail_idx": []
        $publish_id = $res['publish_id'];
        $article_id = $res['article_id'];
        $info = $this->getInfoByPublishAndArticleId($publish_id, $article_id, $authorizer_appid, $component_appid);
        $data = array();
        $data['publish_status'] = $res['publish_status'];
        $data['article_detail'] = empty($res['article_detail']) ? "" : \App\Common\Utils\Helper::myJsonEncode($res['article_detail']);
        $data['fail_idx'] = empty($res['fail_idx']) ? "" : \App\Common\Utils\Helper::myJsonEncode($res['fail_idx']);
        $data['get_time'] = \App\Common\Utils\Helper::getCurrentTime($now);
        if (!empty($info)) {
            return $this->update(array('_id' => $info['_id']), array('$set' => $data));
        } else {
            $data['authorizer_appid'] = $authorizer_appid;
            $data['component_appid'] = $component_appid;
            $data['publish_id'] = $publish_id;
            $data['article_id'] = $article_id;
            $this->insert($data);
        }
    }
}
