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
     */
    public function getInfoByUseridAndTagid($userid, $tagid, $authorizer_appid)
    {
        $query = array();
        $query['userid'] = $userid;
        $query['tagid'] = $tagid;
        $query['authorizer_appid'] = $authorizer_appid;
        $info = $this->findOne($query);

        return $info;
    }

    public function syncTagUserList($tagid, $authorizer_appid, $provider_appid, $res, $now)
    {
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
                $info = $this->getInfoByUseridAndTagid($userid, $tagid, $authorizer_appid);
                $data = array();
                $data['provider_appid'] = $provider_appid;
                $data['tagname'] = $res['tagname'];
                $data['username'] = $username;
                if (isset($res['invalidlist'])) {
                    $data['invalidlist'] = \json_encode($res['invalidlist']);
                } else {
                    $data['invalidlist'] = "[]";
                }
                $data['get_time'] = \App\Common\Utils\Helper::getCurrentTime($now);
                $data['memo'] = \json_encode($res);
                if (!empty($info)) {
                    $this->update(array('_id' => $info['_id']), array('$set' => $data));
                } else {
                    $data['authorizer_appid'] = $authorizer_appid;
                    $data['userid'] = $userid;
                    $data['tagid'] = $tagid;
                    $this->insert($data);
                }
            }
        }
    }
}
