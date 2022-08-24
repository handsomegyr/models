<?php

namespace App\Qyweixin\Models\ExternalContact;

class ContactWay extends \App\Common\Models\Qyweixin\ExternalContact\ContactWay
{

    public function recordConfigId($id, $res, $now)
    {
        $updateData = array();
        $updateData['config_id'] = $res['config_id'];
        $updateData['sync_time'] = \App\Common\Utils\Helper::getCurrentTime($now);
        if (isset($res['qr_code'])) {
            $updateData['qr_code'] = $res['qr_code'];
        }
        return $this->update(array('_id' => $id), array('$set' => $updateData));
    }

    public function recordMediaId($id, $res, $now)
    {
        $updateData = array();
        $updateData['conclusions_image_media_id'] = $res['media_id'];
        $updateData['conclusions_image_media_created_at'] = \App\Common\Utils\Helper::getCurrentTime($res['created_at']);
        $this->update(array('_id' => $id), array('$set' => $updateData));
    }

    public function recordMediaId4Miniprogram($id, $res, $now)
    {
        $updateData = array();
        $updateData['conclusions_miniprogram_pic_media_id'] = $res['media_id'];
        $updateData['conclusions_miniprogram_pic_media_created_at'] = \App\Common\Utils\Helper::getCurrentTime($res['created_at']);
        $this->update(array('_id' => $id), array('$set' => $updateData));
    }

    /**
     * 根据config_id获取信息
     *
     * @param string $config_id
     * @param string $authorizer_appid
     * @param string $provider_appid
     */
    public function getInfoByConfigId($config_id, $authorizer_appid, $provider_appid)
    {
        $query = array();
        $query['config_id'] = $config_id;
        $query['authorizer_appid'] = $authorizer_appid;
        $info = $this->findOne($query);

        return $info;
    }

    public function clearExist($authorizer_appid, $provider_appid, $now)
    {
        $updateData = array('is_exist' => 0);
        $updateData['sync_time'] = \App\Common\Utils\Helper::getCurrentTime($now);
        return $this->update(
            array(
                'authorizer_appid' => $authorizer_appid,
                'provider_appid' => $provider_appid
            ),
            array('$set' => $updateData)
        );
    }

    public function syncContactWayList($authorizer_appid, $provider_appid, $res, $now)
    {
        // {
        //     "errcode": 0,
        //     "errmsg": "ok",
        //      "contact_way":
        //      [
        //          {
        //              "config_id":"534b63270045c9ABiKEE814ef56d91c62f"
        //          }，
        //          {
        //              "config_id":"87bBiKEE811c62f63270041c62f5c9A4ef"
        //          }
        //      ],
        //      "next_cursor":"NEXT_CURSOR"
        //  }
        if (!empty($res['contact_way'])) {
            foreach ($res['contact_way'] as $contactwayInfo) {
                $config_id = $contactwayInfo['config_id'];
                $info = $this->getInfoByConfigId($config_id, $authorizer_appid, $provider_appid);
                $data = array();
                $data['provider_appid'] = $provider_appid;
                $data['sync_time'] = \App\Common\Utils\Helper::getCurrentTime($now);
                // 通过这个字段来表明企业微信那边有这条记录
                $data['is_exist'] = 1;
                if (!empty($info)) {
                    $this->update(array('_id' => $info['_id']), array('$set' => $data));
                } else {
                    $data['authorizer_appid'] = $authorizer_appid;
                    $data['config_id'] = $config_id;
                    $this->insert($data);
                }
            }
        }
    }
}
