<?php

namespace App\Qyweixin\Models\ExternalContact;

class GroupChat extends \App\Common\Models\Qyweixin\ExternalContact\GroupChat
{

    /**
     * 根据客户ID获取信息
     *
     * @param string $chat_id            
     * @param string $authorizer_appid          
     */
    public function getInfoByChatId($chat_id, $authorizer_appid)
    {
        $query = array();
        $query['chat_id'] = $chat_id;
        $query['authorizer_appid'] = $authorizer_appid;
        $info = $this->findOne($query);

        return $info;
    }

    public function syncGroupChatList($authorizer_appid, $provider_appid, $res, $now)
    {
        if (!empty($res['group_chat_list'])) {
            foreach ($res['group_chat_list'] as $group_chat_info) {
                $chat_id = $group_chat_info['chat_id'];
                $status = $group_chat_info['status'];

                $info = $this->getInfoByChatId($chat_id, $authorizer_appid);
                $data = array();
                $data['provider_appid'] = $provider_appid;
                $data['sync_time'] = \App\Common\Utils\Helper::getCurrentTime($now);
                $data['status'] = $status;
                if (!empty($info)) {
                    $this->update(array('_id' => $info['_id']), array('$set' => $data));
                } else {
                    $data['authorizer_appid'] = $authorizer_appid;
                    $data['chat_id'] = $chat_id;
                    $this->insert($data);
                }
            }
        }
    }

    public function updateGroupChatInfoByApi($checkInfo, $groupChatInfo, $now)
    {
        $authorizer_appid = $checkInfo['authorizer_appid'];
        $provider_appid = $checkInfo['provider_appid'];
        $data = $this->getPrepareData($groupChatInfo, $authorizer_appid, $provider_appid, $checkInfo);
        $data['sync_time'] = \App\Common\Utils\Helper::getCurrentTime($now);
        return  $this->update(array('_id' => $checkInfo['_id']), array('$set' => $data));
    }

    private function getPrepareData($groupChatInfo, $authorizer_appid, $provider_appid, $checkInfo)
    {
        $groupChatInfo = $groupChatInfo['group_chat'];
        /**
         * {
         * "errcode": 0,
         * "errmsg": "ok",
         * "group_chat": {
         *      "chat_id": "wrOgQhDgAAMYQiS5ol9G7gK9JVAAAA",
         *      "name": "销售客服群",
         *      "owner": "ZhuShengBen",
         *      "create_time": 1572505490,
         *      "notice" : "文明沟通，拒绝脏话",
         *      "member_list": [{
         *          "userid": "abel",
         *          "type": 1,
         *          "join_time": 1572505491,
         *          "join_scene": 1
         *       }, {
         *           "userid": "sam",
         *          "type": 1,
         *          "join_time": 1572505491,
         *           "join_scene": 1
         *      }, {
         *           "userid": "wmOgQhDgAAuXFJGwbve4g4iXknfOAAAA",
         *           "type": 2,
         *          "join_time": 1572505491,
         *          "join_scene": 1
         *      }]
         *  }
         */
        if (empty($checkInfo)) {
            $data = array();
            $data['authorizer_appid'] = $authorizer_appid;
            $data['external_userid'] = isset($groupChatInfo['external_userid']) ? $groupChatInfo['external_userid'] : '';
            $data['name'] = isset($groupChatInfo['name']) ? $groupChatInfo['name'] : '';
            $data['owner'] = isset($groupChatInfo['owner']) ? $groupChatInfo['owner'] : '';
            $data['create_time'] = \App\Common\Utils\Helper::getCurrentTime($groupChatInfo['create_time']);
            $data['notice'] = isset($groupChatInfo['notice']) ? $groupChatInfo['notice'] : '';
            $data['member_list'] = isset($groupChatInfo['member_list']) ? \json_encode($groupChatInfo['member_list']) : '';
        } else {
            $data = array();
            $data['provider_appid'] = $provider_appid;
            if (isset($groupChatInfo['name'])) {
                $data['name'] = $groupChatInfo['name'];
            }
            if (isset($groupChatInfo['owner'])) {
                $data['owner'] = $groupChatInfo['owner'];
            }
            if (isset($groupChatInfo['create_time'])) {
                $data['create_time'] = \App\Common\Utils\Helper::getCurrentTime($groupChatInfo['create_time']);
            }
            if (isset($groupChatInfo['notice'])) {
                $data['notice'] = $groupChatInfo['notice'];
            }
            if (isset($groupChatInfo['member_list'])) {
                $data['member_list'] = \json_encode($groupChatInfo['member_list']);
            }
        }
        return $data;
    }
}
