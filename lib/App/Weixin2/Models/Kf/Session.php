<?php

namespace App\Weixin2\Models\Kf;

class Session extends \App\Common\Models\Weixin2\Kf\Session
{

    /**
     * 根据完整客服账号获取信息
     *
     * @param string $kf_account            
     * @param string $authorizer_appid            
     * @param string $component_appid          
     */
    public function getInfoByKfAccount($kf_account, $authorizer_appid, $component_appid)
    {
        $info = $this->findOne(array(
            'kf_account' => $kf_account,
            'authorizer_appid' => $authorizer_appid,
            'component_appid' => $component_appid
        ));
        return $info;
    }

    public function updateCreatedStatus($id, $res, $now)
    {
        $updateData = array();
        $updateData['is_created'] = 1;
        $updateData['kfsession_time'] = \App\Common\Utils\Helper::getCurrentTime($now);
        return $this->update(array('_id' => $id), array('$set' => $updateData));
    }

    public function removeCreatedStatus($id, $now)
    {
        $updateData = array();
        $updateData['is_created'] = 0;
        return $this->update(array('_id' => $id), array('$set' => $updateData));
    }

    public function syncKfAccountList($authorizer_appid, $component_appid, $res, $now)
    {
        if (!empty($res['kf_list'])) {
            foreach ($res['kf_list'] as $item) {
                $info = $this->getInfoByKfAccount($item['kf_account'], $authorizer_appid, $component_appid);
                $data = array();
                // [kf_account] => frank@it_intone
                // [kf_headimgurl] =>
                // [kf_id] => 1001
                // [kf_nick] => frank
                // [kf_wx] => dayupanda
                $data['kf_nick'] = $item['kf_nick'];
                $data['kf_id'] = $item['kf_id'];
                $data['kf_headimgurl'] = $item['kf_headimgurl'];

                $data['kf_wx'] = empty($item['kf_wx']) ? "" : $item['kf_wx'];

                if (!empty($item['invite_wx'])) {
                    $data['invite_wx'] = $item['invite_wx'];
                }
                // if (! empty($item['invite_expire_time'])) {
                $data['invite_expire_time'] = empty($item['invite_expire_time']) ? null : \App\Common\Utils\Helper::getCurrentTime($item['invite_expire_time']);
                // }
                // if (! empty($item['invite_status'])) {
                $data['invite_status'] = empty($item['invite_status']) ? "" : $item['invite_status'];
                // }

                $data['is_created'] = 1;
                $data['kfsession_time'] = \App\Common\Utils\Helper::getCurrentTime($now);
                if (!empty($info)) {
                    $this->update(array('_id' => $info['_id']), array('$set' => $data));
                } else {
                    $data['authorizer_appid'] = $authorizer_appid;
                    $data['component_appid'] = $component_appid;
                    $data['kf_account'] = $item['kf_account'];
                    $this->insert($data);
                }
            }
        }
    }

    public function syncOnlineKfAccountList($authorizer_appid, $component_appid, $res, $now)
    {
        if (!empty($res['kf_online_list'])) {
            foreach ($res['kf_online_list'] as $item) {
                $info = $this->getInfoByKfAccount($item['kf_account'], $authorizer_appid, $component_appid);
                $data = array();
                /**
                 * "kf_account" :"test1@test" ,
                 * "status" : 1,
                 * "kf_id" :"1001" ,
                 * "accepted_case" : 1
                 */
                $data['kf_id'] = $item['kf_id'];
                $data['status'] = $item['status'];
                $data['accepted_case'] = $item['accepted_case'];

                $data['is_created'] = 1;
                $data['kfsession_time'] = \App\Common\Utils\Helper::getCurrentTime($now);
                if (!empty($info)) {
                    $this->update(array('_id' => $info['_id']), array('$set' => $data));
                } else {
                    $data['authorizer_appid'] = $authorizer_appid;
                    $data['component_appid'] = $component_appid;
                    $data['kf_account'] = $item['kf_account'];
                    $this->insert($data);
                }
            }
        }
    }
}
