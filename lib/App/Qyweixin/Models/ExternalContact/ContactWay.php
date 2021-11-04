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
}
