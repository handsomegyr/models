<?php

namespace App\Qyweixin\Models\ExternalContact;

class ExternalUserRemark extends \App\Common\Models\Qyweixin\ExternalContact\ExternalUserRemark
{
    
    public function recordMediaId($id, $res, $now)
    {
        $updateData = array();
        $updateData['remark_pic_mediaid'] = $res['media_id'];
        $updateData['remark_pic_media_created_at'] = \App\Common\Utils\Helper::getCurrentTime($res['created_at']);
        $this->update(array('_id' => $id), array('$set' => $updateData));
    }
}
