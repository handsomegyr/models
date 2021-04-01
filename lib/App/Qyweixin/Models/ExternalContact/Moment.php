<?php

namespace App\Qyweixin\Models\ExternalContact;

class Moment extends \App\Common\Models\Qyweixin\ExternalContact\Moment
{
    /**
     * 根据朋友圈id获取信息
     *
     * @param string $moment_id            
     * @param string $authorizer_appid          
     */
    public function getInfoByMomentId($moment_id, $authorizer_appid)
    {
        $query = array();
        $query['moment_id'] = $moment_id;
        $query['authorizer_appid'] = $authorizer_appid;
        $info = $this->findOne($query);

        return $info;
    }

    public function syncMomentList($authorizer_appid, $provider_appid, $res, $now)
    {
        /**
         * {
         * "errcode":0,
         * "errmsg":"ok",
         * "next_cursor":"CURSOR",
         * "moment_list":[
         *     {
         *        "moment_id":"momxxx",
         *         "creator":"xxxx",
         *         "create_time":"xxxx",
         *         "create_type":1,
         *         "visible_type  ":1,
         *         "text":{
         *             "content":"test"
         *         },
         *         "image":[
         *                 {"media_id":"WWCISP_xxxxx"}
         *         ],
         *         "video":{
         *             "media_id":"WWCISP_xxxxx",
         *             "thumb_media_id":"WWCISP_xxxxx"
         *         },
         *         "link":{
         *             "title":"腾讯网-QQ.COM",
         *             "url":"https://www.qq.com"
         *         },
         *         "location":{
         *             "latitude":"23.10647",
         *             "longitude":"113.32446",
         *             "name":"广州市 · 广州塔"
         *         }
         *     }
         * ]
         * }
         */
        if (!empty($res['moment_list'])) {
            foreach ($res['moment_list'] as $momentInfo) {
                $moment_id = $momentInfo['moment_id'];
                $create_time = $momentInfo['create_time'];
                $info = $this->getInfoByMomentId($moment_id, $authorizer_appid);
                $data = array();
                $data['provider_appid'] = $provider_appid;
                $data['creator'] = $momentInfo['creator'];
                $data['create_type'] = $momentInfo['create_type'];
                $data['visible_type'] = $momentInfo['visible_type'];
                $data['text_content'] = !isset($momentInfo['text']['content']) ? "" : $momentInfo['text']['content'];
                $data['image_media_id'] = !isset($momentInfo['image']['media_id']) ? "" : $momentInfo['image']['media_id'];
                $data['video_media_id'] = !isset($momentInfo['video']['media_id']) ? "" : $momentInfo['video']['media_id'];
                $data['video_thumb_media_id'] = !isset($momentInfo['video']['thumb_media_id']) ? "" : $momentInfo['video']['thumb_media_id'];;
                $data['link_title'] = !isset($momentInfo['link']['title']) ? "" : $momentInfo['link']['title'];
                $data['link_url'] = !isset($momentInfo['link']['url']) ? "" : $momentInfo['link']['url'];
                $data['location_latitude'] = !isset($momentInfo['location']['latitude']) ? "0.000000" : $momentInfo['location']['latitude'];
                $data['location_longitude'] = !isset($momentInfo['location']['longitude']) ? "0.000000" : $momentInfo['location']['longitude'];
                $data['location_name'] = !isset($momentInfo['location']['name']) ? "" : $momentInfo['location']['name'];
                $data['create_time'] = \App\Common\Utils\Helper::getCurrentTime($create_time);
                if (!empty($info)) {
                    $this->update(array('_id' => $info['_id']), array('$set' => $data));
                } else {
                    $data['authorizer_appid'] = $authorizer_appid;
                    $data['moment_id'] = $moment_id;
                    $this->insert($data);
                }
            }
        }
    }
}
