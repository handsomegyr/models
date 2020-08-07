<?php

namespace App\Weixin2\Models\Miniprogram\Qrcode;

class Qrcode extends \App\Common\Models\Weixin2\Miniprogram\Qrcode\Qrcode
{
    public function recordQrcode($id, $url, $now, $is_auto, $channel, $name)
    {
        $updateData = array();
        $updateData['url'] = $url;
        $updateData['qrcode_time'] = \App\Common\Utils\Helper::getCurrentTime($now);
        $updateData['is_created'] = true;
        $updateData['is_auto'] = $is_auto;
        $updateData['channel'] = $channel;
        $updateData['name'] = $name;
        return $this->update(array('_id' => $id), array('$set' => $updateData));
    }
}
