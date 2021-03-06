<?php

namespace App\Qyweixin\Models\ExternalContact;

class Transfer extends \App\Common\Models\Qyweixin\ExternalContact\Transfer
{

    /**
     * 根据客户ID获取信息
     *
     * @param string $external_userid      
     * @param string $handover_userid       
     * @param string $takeover_userid      
     * @param string $authorizer_appid          
     */
    public function getInfoByUserId($external_userid, $handover_userid, $takeover_userid, $authorizer_appid)
    {
        $query = array();
        $query['external_userid'] = $external_userid;
        $query['handover_userid'] = $handover_userid;
        $query['takeover_userid'] = $takeover_userid;
        $query['authorizer_appid'] = $authorizer_appid;
        $info = $this->findOne($query);

        return $info;
    }

    public function recordTransferResult($id, $res, $now)
    {
        $updateData = array();
        $updateData['is_transfered'] = 1;
        $updateData['transfer_time'] = \App\Common\Utils\Helper::getCurrentTime($now);
        $updateData['memo'] = \json_encode($res);
        $this->update(array('_id' => $id), array('$set' => $updateData));
    }
}
