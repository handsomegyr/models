<?php

namespace App\Weixin2\Models;

class Shorturl extends \App\Common\Models\Weixin2\Shorturl
{

    public function updateCreatedStatus($id, $res, $now)
    {
        $updateData = array();
        $updateData['is_created'] = 1;
        $updateData['short_url'] = $res['short_url'];
        $updateData['short_url_time'] = \App\Common\Utils\Helper::getCurrentTime($now);
        return $this->update(array('_id' => $id), array('$set' => $updateData));
    }

    public function removeCreatedStatus($id, $now)
    {
        $updateData = array();
        $updateData['is_created'] = 0;
        $updateData['short_url'] = "";
        return $this->update(array('_id' => $id), array('$set' => $updateData));
    }
}
