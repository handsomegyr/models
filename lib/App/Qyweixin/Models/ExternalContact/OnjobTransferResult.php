<?php

namespace App\Qyweixin\Models\ExternalContact;

class OnjobTransferResult extends \App\Common\Models\Qyweixin\ExternalContact\OnjobTransferResult
{
    /**
     * 根据客户ID获取信息
     *      
     * @param string $external_userid
     * @param string $handover_userid 
     * @param string $takeover_userid
     * @param string $agentid
     * @param string $authorizer_appid
     * @param string $provider_appid
     */
    public function getInfoByUserId($external_userid, $handover_userid, $takeover_userid, $agentid, $authorizer_appid, $provider_appid)
    {
        $query = array();
        $query['external_userid'] = $external_userid;
        $query['handover_userid'] = $handover_userid;
        $query['takeover_userid'] = $takeover_userid;
        $query['agentid'] = $agentid;
        $query['authorizer_appid'] = $authorizer_appid;
        $query['provider_appid'] = $provider_appid;
        $info = $this->findOne($query);

        return $info;
    }

    public function syncTransferResult($handover_userid, $takeover_userid, $agentid, $authorizer_appid, $provider_appid, $res, $now)
    {
        // {
        //     "errcode": 0,
        //     "errmsg": "ok",
        //     "customer":
        //    [
        //    {
        //        "external_userid":"woAJ2GCAAAXtWyujaWJHDDGi0mACCCC",
        //        "status":1,
        //        "takeover_time":1588262400
        //    },
        //    {
        //        "external_userid":"woAJ2GCAAAXtWyujaWJHDDGi0mACBBBB",
        //        "status":2,
        //        "takeover_time":1588482400
        //    },
        //    {
        //        "external_userid":"woAJ2GCAAAXtWyujaWJHDDGi0mACAAAA",
        //        "status":3,
        //        "takeover_time":0
        //    }
        //    ],
        //    "next_cursor":"NEXT_CURSOR"
        //  }

        if (!empty($res['customer'])) {
            foreach ($res['customer'] as $customerinfo) {
                $external_userid = $customerinfo['external_userid'];
                $status = $customerinfo['status'];
                $takeover_time = $customerinfo['takeover_time'];
                $info = $this->getInfoByUserId($external_userid, $handover_userid, $takeover_userid, $agentid, $authorizer_appid, $provider_appid);
                $data = array();
                $data['status'] = $status;
                $data['takeover_time'] = \App\Common\Utils\Helper::getCurrentTime($takeover_time);
                $data['sync_time'] = \App\Common\Utils\Helper::getCurrentTime($now);
                if (!empty($info)) {
                    $this->update(array('_id' => $info['_id']), array('$set' => $data));
                } else {
                    $data['provider_appid'] = $provider_appid;
                    $data['authorizer_appid'] = $authorizer_appid;
                    $data['agentid'] = $agentid;
                    $data['external_userid'] = $external_userid;
                    $data['handover_userid'] = $handover_userid;
                    $data['takeover_userid'] = $takeover_userid;
                    $this->insert($data);
                }
            }
        }
    }
}
