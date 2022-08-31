<?php

namespace App\Qyweixin\Models\ExternalContact;

class ContactWay extends \App\Common\Models\Qyweixin\ExternalContact\ContactWay
{

    public function recordConfigId($id, $res, $now)
    {
        $updateData = array();
        $updateData['config_id'] = $res['config_id'];
        $updateData['is_exist'] = 1;
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
     * @param string $agentid
     */
    public function getInfoByConfigId($config_id, $authorizer_appid, $provider_appid, $agentid)
    {
        $query = array();
        $query['config_id'] = $config_id;
        $query['agentid'] = $agentid;
        $query['authorizer_appid'] = $authorizer_appid;
        $query['provider_appid'] = $provider_appid;
        $info = $this->findOne($query);

        return $info;
    }

    public function clearExist($authorizer_appid, $provider_appid, $agentid, $now)
    {
        $updateData = array('is_exist' => 0);
        $updateData['sync_time'] = \App\Common\Utils\Helper::getCurrentTime($now);
        return $this->update(
            array(
                'agentid' => $agentid,
                'authorizer_appid' => $authorizer_appid,
                'provider_appid' => $provider_appid
            ),
            array('$set' => $updateData)
        );
    }

    public function syncContactWayList($authorizer_appid, $provider_appid, $agentid, $res, $now)
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
                $info = $this->getInfoByConfigId($config_id, $authorizer_appid, $provider_appid, $agentid);
                $data = array();
                $data['sync_time'] = \App\Common\Utils\Helper::getCurrentTime($now);
                // 通过这个字段来表明企业微信那边有这条记录
                $data['is_exist'] = 1;
                if (!empty($info)) {
                    $this->update(array('_id' => $info['_id']), array('$set' => $data));
                } else {
                    $data['provider_appid'] = $provider_appid;
                    $data['authorizer_appid'] = $authorizer_appid;
                    $data['agentid'] = $agentid;
                    $data['config_id'] = $config_id;
                    $this->insert($data);
                }
            }
        }
    }

    public function recordContactWayInfo($contactWayInfo, $res, $now)
    {
        // {
        //     "errcode": 0,
        //     "errmsg": "ok",
        //     "contact_way":
        //      {
        //          "config_id":"42b34949e138eb6e027c123cba77fAAA",
        //          "type":1,
        //          "scene":1,
        //          "style":2,
        //          "remark":"test remark",
        //          "skip_verify":true,
        //          "state":"teststate",
        //          "qr_code":"http://p.qpic.cn/wwhead/duc2TvpEgSdicZ9RrdUtBkv2UiaA/0",
        //          "user" : ["zhangsan", "lisi", "wangwu"],
        //          "party" : [2, 3],
        //          "is_temp":true,
        //          "expires_in":86400,
        //          "chat_expires_in":86400,
        //          "unionid":"oxTWIuGaIt6gTKsQRLau2M0AAAA",
        //          "conclusions":
        //      }
        //  }
        $updateData = array();
        if (isset($res['contact_way']['config_id'])) {
            $updateData['config_id'] = $res['contact_way']['config_id'];
        }
        if (isset($res['contact_way']['type'])) {
            $updateData['type'] = intval($res['contact_way']['type']);
        }
        if (isset($res['contact_way']['scene'])) {
            $updateData['scene'] = intval($res['contact_way']['scene']);
        }
        if (isset($res['contact_way']['style'])) {
            $updateData['style'] = intval($res['contact_way']['style']);
        }
        if (isset($res['contact_way']['remark'])) {
            $updateData['remark'] = $res['contact_way']['remark'];
        }
        if (isset($res['contact_way']['skip_verify'])) {
            $updateData['skip_verify'] = intval($res['contact_way']['skip_verify']);
        }
        if (isset($res['contact_way']['state'])) {
            $updateData['state'] = $res['contact_way']['state'];
        }
        if (isset($res['contact_way']['qr_code'])) {
            $updateData['qr_code'] = $res['contact_way']['qr_code'];
        }
        if (isset($res['contact_way']['user'])) {
            $updateData['user'] = \App\Common\Utils\Helper::myJsonEncode($res['contact_way']['user']);
        }
        if (isset($res['contact_way']['party'])) {
            $updateData['party'] = \App\Common\Utils\Helper::myJsonEncode($res['contact_way']['party']);
        }
        if (isset($res['contact_way']['is_temp'])) {
            $updateData['is_temp'] = intval($res['contact_way']['is_temp']);
        }
        if (isset($res['contact_way']['expires_in'])) {
            $updateData['expires_in'] = $res['contact_way']['expires_in'];
        }
        if (isset($res['contact_way']['chat_expires_in'])) {
            $updateData['chat_expires_in'] = $res['contact_way']['chat_expires_in'];
        }
        if (isset($res['contact_way']['unionid'])) {
            $updateData['unionid'] = $res['contact_way']['unionid'];
        }
        if (isset($res['contact_way']['conclusions'])) {
            $updateData['conclusions'] = \App\Common\Utils\Helper::myJsonEncode($res['contact_way']['conclusions']);
        }
        $updateData['is_exist'] = 1;
        $updateData['sync_time'] = \App\Common\Utils\Helper::getCurrentTime($now);
        return $this->update(array('_id' => $contactWayInfo['_id']), array('$set' => $updateData));
    }
}
