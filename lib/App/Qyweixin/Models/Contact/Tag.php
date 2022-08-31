<?php

namespace App\Qyweixin\Models\Contact;

class Tag extends \App\Common\Models\Qyweixin\Contact\Tag
{

    /**
     * 根据标签ID获取信息
     *
     * @param string $tagid
     * @param string $authorizer_appid
     * @param string $provider_appid
     * @param string $agentid
     */
    public function getInfoByTagId($tagid, $authorizer_appid, $provider_appid, $agentid)
    {
        $query = array();
        $query['tagid'] = $tagid;
        $query['authorizer_appid'] = $authorizer_appid;
        $query['provider_appid'] = $provider_appid;
        $query['agentid'] = $agentid;
        $info = $this->findOne($query);

        return $info;
    }

    public function clearExist($authorizer_appid, $provider_appid, $agentid, $now)
    {
        $updateData = array('is_exist' => 0);
        $updateData['sync_time'] = \App\Common\Utils\Helper::getCurrentTime($now);
        return $this->update(
            array(
                'agentid' => $agentid,
                'authorizer_appid' => $authorizer_appid,
                'provider_appid' => $provider_appid
            ),
            array('$set' => $updateData)
        );
    }

    public function syncTagList($authorizer_appid, $provider_appid, $agentid, $res, $now)
    {
        $this->clearExist($authorizer_appid, $provider_appid, $agentid, $now);
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
                $info = $this->getInfoByTagId($tagid, $authorizer_appid, $provider_appid, $agentid);
                $data = array();
                $data['tagname'] = $tagInfo['tagname'];
                $data['sync_time'] = \App\Common\Utils\Helper::getCurrentTime($now);
                $data['is_exist'] = 1;
                if (!empty($info)) {
                    $this->update(array('_id' => $info['_id']), array('$set' => $data));
                } else {
                    $data['provider_appid'] = $provider_appid;
                    $data['authorizer_appid'] = $authorizer_appid;
                    $data['agentid'] = $agentid;
                    $data['tagid'] = $tagid;
                    $this->insert($data);
                }
            }
        }
    }
}
