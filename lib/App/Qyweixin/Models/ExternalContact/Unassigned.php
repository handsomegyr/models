<?php

namespace App\Qyweixin\Models\ExternalContact;

class Unassigned extends \App\Common\Models\Qyweixin\ExternalContact\Unassigned
{

    /**
     * 根据客户ID获取信息
     *
     * @param string $handover_userid      
     * @param string $external_userid       
     * @param string $authorizer_appid          
     */
    public function getInfoByUserId($handover_userid, $external_userid, $authorizer_appid)
    {
        $query = array();
        $query['external_userid'] = $external_userid;
        $query['handover_userid'] = $handover_userid;
        $query['authorizer_appid'] = $authorizer_appid;
        $info = $this->findOne($query);

        return $info;
    }

    public function syncUnassignedList($authorizer_appid, $provider_appid, $res, $now)
    {
        if (!empty($res['info'])) {
            foreach ($res['info'] as $unassigned_info) {
                $handover_userid = $unassigned_info['handover_userid'];
                $external_userid = $unassigned_info['external_userid'];
                $info = $this->getInfoByUserId($handover_userid, $external_userid, $authorizer_appid);
                $data = array();
                $data['provider_appid'] = $provider_appid;
                $data['dimission_time'] = \App\Common\Utils\Helper::getCurrentTime($unassigned_info['dimission_time']);
                $data['sync_time'] = \App\Common\Utils\Helper::getCurrentTime($now);
                if (!empty($info)) {
                    $this->update(array('_id' => $info['_id']), array('$set' => $data));
                } else {
                    $data['authorizer_appid'] = $authorizer_appid;
                    $data['handover_userid'] = $handover_userid;
                    $data['external_userid'] = $external_userid;
                    $this->insert($data);
                }
            }
        }
    }
}
