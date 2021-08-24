<?php

namespace App\Qyweixin\Models\ExternalContact;

class GroupMsgResult extends \App\Common\Models\Qyweixin\ExternalContact\GroupMsgResult
{

    /**
     * 根据外部联系人userid获取信息
     *
     * @param string $msgid 
     * @param string $external_userid 
     * @param string $userid              
     * @param string $authorizer_appid               
     * @param string $agentid         
     */
    public function getInfoByMsgIdAndUser($msgid, $external_userid, $userid, $authorizer_appid, $agentid)
    {
        $query = array();
        $query['msgid'] = $msgid;
        $query['external_userid'] = $external_userid;
        $query['userid'] = $userid;
        $query['authorizer_appid'] = $authorizer_appid;
        $query['agentid'] = $agentid;
        $info = $this->findOne($query);

        return $info;
    }

    public function syncDetailList($msgid, $authorizer_appid, $provider_appid, $agentid, $res, $now)
    {
        /**
         *{
         *"errcode": 0,
         *"errmsg": "ok",
         *"check_status": 1,
         *"detail_list": [
         *    {
         *        "external_userid": "wmqfasd1e19278asdasAAAA",
         *        "chat_id":"wrOgQhDgAAMYQiS5ol9G7gK9JVAAAA",
         *        "userid": "zhangsan",
         *        "status": 1,
         *        "send_time": 1552536375
         *    }
         *]
         *}
         */
        if (!empty($res['detail_list'])) {
            foreach ($res['detail_list'] as $useridInfo) {

                $external_userid = isset($useridInfo['external_userid']) ? $useridInfo['external_userid'] : '';
                $userid = isset($useridInfo['userid']) ? $useridInfo['userid'] : '';

                $data = array();
                $data['provider_appid'] = $provider_appid;
                $data['chat_id'] = isset($useridInfo['chat_id']) ? $useridInfo['chat_id'] : '';
                $data['status'] = isset($useridInfo['status']) ? intval($useridInfo['status']) : 0;
                if (isset($useridInfo['send_time'])) {
                    $data['send_time'] = \App\Common\Utils\Helper::getCurrentTime($useridInfo['send_time']);
                }
                $info = $this->getInfoByMsgIdAndUser($msgid, $external_userid, $userid, $authorizer_appid, $agentid);

                if (!empty($info)) {
                    $this->update(array('_id' => $info['_id']), array('$set' => $data));
                } else {
                    $data['authorizer_appid'] = $authorizer_appid;
                    $data['agentid'] = $agentid;
                    $data['msgid'] = $msgid;
                    $data['external_userid'] = $external_userid;
                    $data['userid'] = $userid;
                    $this->insert($data);
                }
            }
        }
    }
}
