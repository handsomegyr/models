<?php

namespace App\Qyweixin\Models\Media;

class Media extends \App\Common\Models\Qyweixin\Media\Media
{

    public function recordMediaId($id, $res, $now)
    {
        $updateData = array();
        $updateData['media_id'] = $res['media_id'];
        $updateData['media_time'] = \App\Common\Utils\Helper::getCurrentTime($res['created_at']);
        return $this->update(array('_id' => $id), array('$set' => $updateData));
    }
}
