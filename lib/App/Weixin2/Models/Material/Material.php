<?php

namespace App\Weixin2\Models\Material;

class Material extends \App\Common\Models\Weixin2\Material\Material
{

    public function recordMediaId($id, $res, $now)
    {
        $updateData = array();
        $updateData['media_id'] = $res['media_id'];
        if (!empty($res['url'])) {
            $updateData['url'] = $res['url'];
        }
        $updateData['media_time'] = \App\Common\Utils\Helper::getCurrentTime($now);
        return $this->update(array('_id' => $id), array('$set' => $updateData));
    }

    public function removeMediaId($id, $res, $now)
    {
        $updateData = array();
        $updateData['media_id'] = "";
        $updateData['url'] = "";
        $updateData['delete_media_time'] = \App\Common\Utils\Helper::getCurrentTime($now);
        return $this->update(array('_id' => $id), array('$set' => $updateData));
    }
}
