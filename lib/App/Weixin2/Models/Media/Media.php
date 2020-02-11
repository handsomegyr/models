<?php

namespace App\Weixin2\Models\Media;

class Media extends \App\Common\Models\Weixin2\Media\Media
{

    public function recordMediaId($id, $res, $now)
    {
        $updateData = array();
        $updateData['media_id'] = $res['media_id'];
        $updateData['media_time'] = date("Y-m-d H:i:s", $res['created_at']);
        return $this->updateById($id, $updateData);
    }
}
