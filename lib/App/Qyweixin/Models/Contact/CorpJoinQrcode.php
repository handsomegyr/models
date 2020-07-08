<?php

namespace App\Qyweixin\Models\Contact;

class CorpJoinQrcode extends \App\Common\Models\Qyweixin\Contact\CorpJoinQrcode
{
    public function recordJoinQrcode($id, $res, $now)
    {
        $updateData = array();
        $updateData['is_created'] = true;
        $updateData['join_qrcode'] = $res['join_qrcode'];
        $updateData['create_time'] = \App\Common\Utils\Helper::getCurrentTime($now);
        return $this->update(array('_id' => $id), array('$set' => $updateData));
    }
}
