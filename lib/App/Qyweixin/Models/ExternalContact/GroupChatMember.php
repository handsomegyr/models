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

    public function syncMemberList($chat_id, $authorizer_appid, $provider_appid, $res, $now)
    {
        /**
         * {
         *  "userid": "abel",
         *  "type": 1,
         *  "join_time": 1572505491,
         *  "join_scene": 1
         *},
         */
        if (!empty($res['group_chat']['member_list'])) {
            foreach ($res['group_chat']['member_list'] as $memberInfo) {
                $userid = $memberInfo['userid'];
                $data = array();
                $data['provider_appid'] = $provider_appid;
                $data['sync_time'] = \App\Common\Utils\Helper::getCurrentTime($now);
                $data['type'] = isset($memberInfo['type']) ? intval($memberInfo['type']) : 0;
                $data['join_scene'] = isset($memberInfo['join_scene']) ? intval($memberInfo['join_scene']) : 0;
                $data['join_time'] = \App\Common\Utils\Helper::getCurrentTime($memberInfo['join_time']);

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
