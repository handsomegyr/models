<?php

namespace App\Qyweixin\Models\ExternalContact;

class WelcomeMsg extends \App\Common\Models\Qyweixin\ExternalContact\WelcomeMsg
{

    public function recordMediaId($id, $res, $now)
    {
        $updateData = array();
        $updateData['image_media_id'] = $res['media_id'];
        $updateData['image_media_created_at'] = \App\Common\Utils\Helper::getCurrentTime($res['created_at']);
        $this->update(array('_id' => $id), array('$set' => $updateData));
    }

    public function recordMediaId4Miniprogram($id, $res, $now)
    {
        $updateData = array();
        $updateData['miniprogram_pic_media_id'] = $res['media_id'];
        $updateData['miniprogram_pic_media_created_at'] = \App\Common\Utils\Helper::getCurrentTime($res['created_at']);
        $this->update(array('_id' => $id), array('$set' => $updateData));
    }
}
