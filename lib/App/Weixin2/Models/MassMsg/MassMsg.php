<?php

namespace App\Weixin2\Models\MassMsg;

class MassMsg extends \App\Common\Models\Weixin2\MassMsg\MassMsg
{

    public function recordUploadResult($id, $res, $now)
    {
        $updateData = array();
        $updateData['upload_media_id'] = $res['media_id'];
        $updateData['upload_media_created_at'] = $res['created_at'];
        $updateData['upload_media_type'] = $res['type'];
        return $this->updateById($id, $updateData);
    }

    public function recordMsgId($id, $res, $now)
    {
        $updateData = array();
        $updateData['msg_id'] = $res['msg_id'];
        if (!empty($res['msg_data_id'])) {
            $updateData['msg_data_id'] = $res['msg_data_id'];
        }
        $updateData['msg_time'] = date("Y-m-d H:i:s", $now);
        return $this->updateById($id, $updateData);
    }

    public function recordMsgStatus($id, $res)
    {
        $updateData = array();
        $updateData['msg_status'] = $res['msg_status'];
        return $this->updateById($id, $updateData);
    }

    public function removeMsgId($id)
    {
        $updateData = array();
        $updateData['msg_id'] = "";
        $updateData['msg_data_id'] = "";
        return $this->updateById($id, $updateData);
    }
}
