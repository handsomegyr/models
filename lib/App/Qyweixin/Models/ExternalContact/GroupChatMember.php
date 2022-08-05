<?php

namespace App\Qyweixin\Models\ExternalContact;

class GroupChatMember extends \App\Common\Models\Qyweixin\ExternalContact\GroupChatMember
{

    /**
     * 根据userid获取信息
     *
     * @param string $userid 
     * @param string $chat_id              
     * @param string $authorizer_appid          
     */
    public function getInfoByUserId($userid, $chat_id, $authorizer_appid)
    {
        $query = array();
        $query['userid'] = $userid;
        $query['chat_id'] = $chat_id;
        $query['authorizer_appid'] = $authorizer_appid;
        $info = $this->findOne($query);

        return $info;
    }

    public function clearExist($chat_id, $authorizer_appid, $provider_appid, $now)
    {
        $updateData = array('is_exist' => 0);
        $updateData['sync_time'] = \App\Common\Utils\Helper::getCurrentTime($now);
        return $this->update(
            array(
                'chat_id' => $chat_id,
                'authorizer_appid' => $authorizer_appid,
                'provider_appid' => $provider_appid
            ),
            array('$set' => $updateData)
        );
    }

    public function syncMemberList($chat_id, $authorizer_appid, $provider_appid, $res, $now)
    {
        /**
         * {
         *  "userid": "abel",
         *  "type": 1,
         *  "join_time": 1572505491,
         *  "join_scene": 1,
         *  "invitor": {
         * 	    "userid": "jack"
         *  },
         *  "group_nickname" : "客服小张",
         *  "name" : "张三丰"
         *},
         */
        if (!empty($res['group_chat']['member_list'])) {
            foreach ($res['group_chat']['member_list'] as $memberInfo) {
                $userid = $memberInfo['userid'];
                $data = array();
                $data['provider_appid'] = $provider_appid;
                $data['is_exist'] = 1;
                $data['sync_time'] = \App\Common\Utils\Helper::getCurrentTime($now);
                $data['type'] = isset($memberInfo['type']) ? intval($memberInfo['type']) : 0;
                $data['join_scene'] = isset($memberInfo['join_scene']) ? intval($memberInfo['join_scene']) : 0;
                $data['join_time'] = \App\Common\Utils\Helper::getCurrentTime($memberInfo['join_time']);
                $data['invitor'] = isset($memberInfo['invitor']) ? \App\Common\Utils\Helper::myJsonEncode($memberInfo['invitor']) : "{}";
                $data['group_nickname'] = isset($memberInfo['group_nickname']) ? trim($memberInfo['group_nickname']) : "";
                $data['name'] = isset($memberInfo['name']) ? trim($memberInfo['name']) : "";
                $info = $this->getInfoByUserId($userid, $chat_id, $authorizer_appid);

                if (!empty($info)) {
                    $this->update(array('_id' => $info['_id']), array('$set' => $data));
                } else {
                    $data['authorizer_appid'] = $authorizer_appid;
                    $data['userid'] = $userid;
                    $data['chat_id'] = $chat_id;
                    $this->insert($data);
                }
            }
        }
    }
}
