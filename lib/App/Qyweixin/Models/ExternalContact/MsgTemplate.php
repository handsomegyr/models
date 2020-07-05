<?php

namespace App\Qyweixin\Models\ExternalContact;

class MsgTemplate extends \App\Common\Models\Qyweixin\ExternalContact\MsgTemplate
{
    public function recordMsgId($id, $res, $now)
    {
        $updateData = array();
        $updateData['msgid'] = $res['msgid'];
        $updateData['fail_list'] = \json_encode($res['fail_list']);
        $updateData['send_time'] = \App\Common\Utils\Helper::getCurrentTime($now);
        return $this->update(array('_id' => $id), array('$set' => $updateData));
    }

    public function recordGroupMsgResult($id, $res, $now)
    {
        $updateData = array();
        $updateData['check_status'] = $res['check_status'];
        $updateData['detail_list'] = \json_encode($res['detail_list']);
        $updateData['get_result_time'] = \App\Common\Utils\Helper::getCurrentTime($now);
        return $this->update(array('_id' => $id), array('$set' => $updateData));
    }

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
