<?php

namespace App\Qyweixin\Models\ExternalContact;

class GroupMsgTask extends \App\Common\Models\Qyweixin\ExternalContact\GroupMsgTask
{

    /**
     * 根据userid获取信息
     *
     * @param string $msgid
     * @param string $userid              
     * @param string $authorizer_appid               
     * @param string $agentid         
     */
    public function getInfoByMsgIdAndUser($msgid, $userid, $authorizer_appid, $agentid)
    {
        $query = array();
        $query['msgid'] = $msgid;
        $query['userid'] = $userid;
        $query['authorizer_appid'] = $authorizer_appid;
        $query['agentid'] = $agentid;
        $info = $this->findOne($query);

        return $info;
    }

    public function syncTaskList($msgid, $authorizer_appid, $provider_appid, $agentid, $res, $now)
    {
        /**
         *{
         *"errcode": 0,
         *"errmsg": "ok",
         *"next_cursor":"CURSOR",
         *"task_list": [
         *    {
         *        "userid": "zhangsan",
         *        "status": 1,
         *        "send_time": 1552536375
         *    }
         *]
         *}
         */
        if (!empty($res['task_list'])) {
            foreach ($res['task_list'] as $useridInfo) {

                $userid = $useridInfo['userid'];

                $data = array();
                $data['provider_appid'] = $provider_appid;
                $data['status'] = isset($useridInfo['status']) ? intval($useridInfo['status']) : 0;
                if (isset($useridInfo['send_time'])) {
                    $data['send_time'] = \App\Common\Utils\Helper::getCurrentTime($useridInfo['send_time']);
                }
                $info = $this->getInfoByMsgIdAndUser($msgid, $userid, $authorizer_appid, $agentid);

                if (!empty($info)) {
                    $this->update(array('_id' => $info['_id']), array('$set' => $data));
                } else {
                    $data['authorizer_appid'] = $authorizer_appid;
                    $data['agentid'] = $agentid;
                    $data['msgid'] = $msgid;
                    $data['userid'] = $userid;
                    $this->insert($data);
                }
            }
        }
    }
}
