<?php

namespace App\Qyweixin\Models\Contact;

class TagUser extends \App\Common\Models\Qyweixin\Contact\TagUser
{
    /**
     * 根据用户ID和标签ID获取信息
     *
     * @param string $userid
     * @param string $tagid
     * @param string $authorizer_appid
     * @param string $provider_appid
     * @param string $agentid
     */
    public function getInfoByUseridAndTagid($userid, $tagid, $authorizer_appid, $provider_appid, $agentid)
    {
        $query = array();
        $query['userid'] = $userid;
        $query['tagid'] = $tagid;
        $query['agentid'] = $agentid;
        $query['authorizer_appid'] = $authorizer_appid;
        $query['provider_appid'] = $provider_appid;
        $info = $this->findOne($query);

        return $info;
    }

    public function clearExist($tagid, $authorizer_appid, $provider_appid, $agentid, $now)
    {
        $updateData = array('is_exist' => 0);
        $updateData['get_time'] = \App\Common\Utils\Helper::getCurrentTime($now);
        return $this->update(
            array(
                'tagid' => $tagid,
                'agentid' => $agentid,
                'authorizer_appid' => $authorizer_appid,
                'provider_appid' => $provider_appid
            ),
            array('$set' => $updateData)
        );
    }

    public function syncTagUserList($tagid, $authorizer_appid, $provider_appid, $agentid, $res, $now)
    {
        $this->clearExist($tagid, $authorizer_appid, $provider_appid, $agentid, $now);
        /**
         * {
         * "errcode": 0,
         * "errmsg": "ok",
         * "tagname": "乒乓球协会",
         * "userlist": [
         * {
         *  "userid": "zhangsan",
         *  "name": "李四"
         * }
         * ],
         * "partylist": [2]
         * }
         */
        if (!empty($res['userlist'])) {
            foreach ($res['userlist'] as $userInfo) {
                $userid = $userInfo['userid'];
                $username = $userInfo['name'];
                $info = $this->getInfoByUseridAndTagid($userid, $tagid, $authorizer_appid, $provider_appid, $agentid);
                $data = array();
                $data['tagname'] = $res['tagname'];
                $data['username'] = $username;
                if (isset($res['invalidlist'])) {
                    $data['invalidlist'] = \App\Common\Utils\Helper::myJsonEncode($res['invalidlist']);
                } else {
                    $data['invalidlist'] = "[]";
                }
                $data['is_exist'] = 1;
                $data['get_time'] = \App\Common\Utils\Helper::getCurrentTime($now);
                $data['memo'] = \App\Common\Utils\Helper::myJsonEncode($res);
                if (!empty($info)) {
                    $this->update(array('_id' => $info['_id']), array('$set' => $data));
                } else {
                    $data['provider_appid'] = $provider_appid;
                    $data['authorizer_appid'] = $authorizer_appid;
                    $data['agentid'] = $agentid;
                    $data['userid'] = $userid;
                    $data['tagid'] = $tagid;
                    $this->insert($data);
                }
            }
        }
    }
}
