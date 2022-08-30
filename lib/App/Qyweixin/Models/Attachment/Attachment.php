<?php

namespace App\Qyweixin\Models\Attachment;

class Attachment extends \App\Common\Models\Qyweixin\Attachment\Attachment
{
    /**
     * 获取信息
     *
     * @return array
     */
    public function getInfoByMediaId($media_id, $provider_appid, $authorizer_appid, $agentid)
    {
        $info = $this->findOne(array(
            'media_id' => $media_id,
            'agentid' => $agentid,
            'authorizer_appid' => $authorizer_appid,
            'provider_appid' => $provider_appid
        ));
        return $info;
    }

    /**
     * 获取信息
     *
     * @return array
     */
    public function getInfoByMedia($provider_appid, $authorizer_appid, $agentid, $media, $attachment_type, $media_type)
    {
        $info = $this->findOne(array(
            'media' => $media,
            'attachment_type' => $attachment_type,
            'media_type' => $media_type,
            'agentid' => $agentid,
            'authorizer_appid' => $authorizer_appid,
            'provider_appid' => $provider_appid
        ));
        return $info;
    }

    public function createMedia($provider_appid, $authorizer_appid, $agentid, $name, $attachment_type, $media_type, $media)
    {
        $data = array();
        $data['provider_appid'] = $provider_appid;
        $data['authorizer_appid'] = $authorizer_appid;
        $data['agentid'] = $agentid;
        $data['name'] = $name;
        $data['attachment_type'] = $attachment_type;
        $data['media_type'] = $media_type;
        $data['media'] = $media;
        return $this->insert($data);
    }

    public function recordMediaId($id, $res, $now)
    {
        $updateData = array();
        $updateData['media_id'] = $res['media_id'];
        $updateData['media_time'] = \App\Common\Utils\Helper::getCurrentTime($res['created_at']);
        return $this->update(array('_id' => $id), array('$set' => $updateData));
    }
}
