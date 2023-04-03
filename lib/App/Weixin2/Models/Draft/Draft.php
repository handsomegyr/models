<?php

namespace App\Weixin2\Models\Draft;

class Draft extends \App\Common\Models\Weixin2\Draft\Draft
{

    public function recordMediaId($id, $res, $now)
    {
        $updateData = array();
        $updateData['media_id'] = $res['media_id'];
        $updateData['media_time'] = \App\Common\Utils\Helper::getCurrentTime($now);
        return $this->update(array('_id' => $id), array('$set' => $updateData));
    }

    public function removeMediaId($id)
    {
        $updateData = array();
        $updateData['media_id'] = "";
        return $this->update(array('_id' => $id), array('$set' => $updateData));
    }

    public function recordFreePublishResult($id, $res, $now)
    {
        $updateData = array();
        $updateData['publish_id'] = $res['publish_id'];
        if (isset($res['msg_data_id'])) {
            $updateData['msg_data_id'] = $res['msg_data_id'];
        }
        $updateData['publish_time'] = \App\Common\Utils\Helper::getCurrentTime($now);
        return $this->update(array('_id' => $id), array('$set' => $updateData));
    }
}
