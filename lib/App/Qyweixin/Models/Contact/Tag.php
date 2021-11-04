<?php

namespace App\Qyweixin\Models\Contact;

class Tag extends \App\Common\Models\Qyweixin\Contact\Tag
{

    /**
     * 根据标签ID获取信息
     *
     * @param string $tagid            
     * @param string $authorizer_appid          
     */
    public function getInfoByTagId($tagid, $authorizer_appid)
    {
        $query = array();
        $query['tagid'] = $tagid;
        $query['authorizer_appid'] = $authorizer_appid;
        $info = $this->findOne($query);

        return $info;
    }

    public function clearExist($authorizer_appid, $provider_appid)
    {
        $updateData = array('is_exist' => 0);
        return $this->update(
            array(
                'authorizer_appid' => $authorizer_appid,
                'provider_appid' => $provider_appid
            ),
            array('$set' => $updateData)
        );
    }

    public function syncTagList($authorizer_appid, $provider_appid, $res, $now)
    {
        $this->clearExist($authorizer_appid, $provider_appid);
        /**
         * {
         * "errcode": 0,
         * "errmsg": "ok",
         * "taglist":[
         *      {"tagid":1,"tagname":"a"},
         *      {"tagid":2,"tagname":"b"}
         *  ]
         * }
         */
        if (!empty($res['taglist'])) {
            foreach ($res['taglist'] as $tagInfo) {
                $tagid = $tagInfo['tagid'];
                $info = $this->getInfoByTagId($tagid, $authorizer_appid);
                $data = array();
                $data['provider_appid'] = $provider_appid;
                $data['tagname'] = $tagInfo['tagname'];
                $data['sync_time'] = \App\Common\Utils\Helper::getCurrentTime($now);
                $data['is_exist'] = 1;
                if (!empty($info)) {
                    $this->update(array('_id' => $info['_id']), array('$set' => $data));
                } else {
                    $data['authorizer_appid'] = $authorizer_appid;
                    $data['tagid'] = $tagid;
                    $this->insert($data);
                }
            }
        }
    }
}
