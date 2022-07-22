<?php

namespace App\Qyweixin\Models\Media;

class Media extends \App\Common\Models\Qyweixin\Media\Media
{
    /**
     * 获取信息
     *
     * @return array
     */
    public function getInfoByMediaId($media_id, $authorizer_appid)
    {
        $info = $this->findOne(array(
            'media_id' => $media_id,
            'authorizer_appid' => $authorizer_appid
        ));
        return $info;
    }

    /**
     * 获取信息
     *
     * @return array
     */
    public function getInfoByMedia($provider_appid, $authorizer_appid, $agentid, $media, $type)
    {
        $info = $this->findOne(array(
            'media' => $media,
            'type' => $type,
            'agentid' => $agentid,
            'authorizer_appid' => $authorizer_appid,
            'provider_appid' => $provider_appid
        ));
        return $info;
    }

    public function createMedia($provider_appid, $authorizer_appid, $agentid, $name, $type, $media)
    {
        $data = array();
        $data['provider_appid'] = $provider_appid;
        $data['authorizer_appid'] = $authorizer_appid;
        $data['agentid'] = $agentid;
        $data['name'] = $name;
        $data['type'] = $type;
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
