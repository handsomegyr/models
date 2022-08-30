<?php

namespace App\Qyweixin\Models\ExternalContact;

class OnjobTransferGroupchat extends \App\Common\Models\Qyweixin\ExternalContact\OnjobTransferGroupchat
{

    /**
     * 根据客户ID获取信息
     *
     * @param string $handover_userid
     * @param string $external_userid
     * @param string $agentid
     * @param string $authorizer_appid
     * @param string $provider_appid
     */
    public function getInfoByUserId($handover_userid, $external_userid, $agentid, $authorizer_appid, $provider_appid)
    {
        $query = array();
        $query['external_userid'] = $external_userid;
        $query['handover_userid'] = $handover_userid;
        $query['agentid'] = $agentid;
        $query['authorizer_appid'] = $authorizer_appid;
        $query['provider_appid'] = $provider_appid;
        $info = $this->findOne($query);

        return $info;
    }

    public function syncUnassignedList($agentid, $authorizer_appid, $provider_appid, $res, $now)
    {
        if (!empty($res['info'])) {
            foreach ($res['info'] as $unassigned_info) {
                $handover_userid = $unassigned_info['handover_userid'];
                $external_userid = $unassigned_info['external_userid'];
                $info = $this->getInfoByUserId($handover_userid, $external_userid, $agentid, $authorizer_appid, $provider_appid);
                $data = array();
                $data['dimission_time'] = \App\Common\Utils\Helper::getCurrentTime($unassigned_info['dimission_time']);
                $data['sync_time'] = \App\Common\Utils\Helper::getCurrentTime($now);
                if (!empty($info)) {
                    $this->update(array('_id' => $info['_id']), array('$set' => $data));
                } else {
                    $data['provider_appid'] = $provider_appid;
                    $data['authorizer_appid'] = $authorizer_appid;
                    $data['agentid'] = $agentid;
                    $data['handover_userid'] = $handover_userid;
                    $data['external_userid'] = $external_userid;
                    $this->insert($data);
                }
            }
        }
    }
}
