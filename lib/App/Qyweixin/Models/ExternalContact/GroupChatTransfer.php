<?php

namespace App\Qyweixin\Models\ExternalContact;

class GroupChatTransfer extends \App\Common\Models\Qyweixin\ExternalContact\GroupChatTransfer
{

    /**
     * 根据chat_id获取信息
     *
     * @param string $chat_id
     * @param string $new_owner
     * @param string $agentid 
     * @param string $authorizer_appid 
     * @param string $provider_appid          
     */
    public function getInfoByChatId($chat_id, $new_owner, $agentid, $authorizer_appid, $provider_appid)
    {
        $query = array();
        $query['chat_id'] = $chat_id;
        $query['new_owner'] = $new_owner;
        $query['agentid'] = $agentid;
        $query['authorizer_appid'] = $authorizer_appid;
        $query['provider_appid'] = $provider_appid;
        $info = $this->findOne($query);

        return $info;
    }

    public function recordTransferResult($id, $res, $now)
    {
        $updateData = array();
        if (empty($res['failed_chat_list'])) {
            $updateData['is_transfered'] = 1;
            $updateData['transfer_time'] = \App\Common\Utils\Helper::getCurrentTime($now);
        } else {
            $updateData['failed_chat_id'] = $res['failed_chat_list']['chat_id'];
            $updateData['failed_chat_errcode'] = $res['failed_chat_list']['errcode'];
            $updateData['failed_chat_errmsg'] = $res['failed_chat_list']['errmsg'];
        }
        $updateData['memo'] = \App\Common\Utils\Helper::myJsonEncode($res);
        $this->update(array('_id' => $id), array('$set' => $updateData));
    }
}
