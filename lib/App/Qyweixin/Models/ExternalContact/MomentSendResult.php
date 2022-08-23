<?php

namespace App\Qyweixin\Models\ExternalContact;

class MomentSendResult extends \App\Common\Models\Qyweixin\ExternalContact\MomentSendResult
{
    /**
     * 根据用户ID和朋友圈id获取信息
     *
     * @param string $userid 
     * @param string $external_userid 
     * @param string $moment_id
     * @param string $authorizer_appid
     * @param string $provider_appid         
     */
    public function getInfoByUserIdAndMomentId($userid, $external_userid, $moment_id, $authorizer_appid, $provider_appid)
    {
        $query = array();
        $query['userid'] = $userid;
        $query['external_userid'] = $external_userid;
        $query['moment_id'] = $moment_id;
        $query['authorizer_appid'] = $authorizer_appid;
        $info = $this->findOne($query);
        return $info;
    }

    public function syncMomentCustomerList($userid, $moment_id, $authorizer_appid, $provider_appid, $res, $now)
    {
        // {
        //     "errcode":0,
        //     "errmsg":"ok",
        //     "next_cursor":"CURSOR",
        //     "customer_list":[
        //         {
        //             "external_userid":"woAJ2GCAAAXtWyujaWJHDDGi0mACCCC  "
        //         }
        //     ]
        // }
        if (!empty($res['customer_list'])) {
            foreach ($res['customer_list'] as $customer) {
                $external_userid = $customer['external_userid'];
                $info = $this->getInfoByUserIdAndMomentId($userid, $external_userid, $moment_id, $authorizer_appid, $provider_appid);
                $data = array();
                $data['provider_appid'] = $provider_appid;
                $data['get_time'] = \App\Common\Utils\Helper::getCurrentTime($now);
                if (!empty($info)) {
                    $this->update(array('_id' => $info['_id']), array('$set' => $data));
                } else {
                    $data['authorizer_appid'] = $authorizer_appid;
                    $data['moment_id'] = $moment_id;
                    $data['external_userid'] = $external_userid;
                    $data['userid'] = $userid;
                    $this->insert($data);
                }
            }
        }
    }
}
