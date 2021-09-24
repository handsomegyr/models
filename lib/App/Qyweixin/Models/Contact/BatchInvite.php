<?php

namespace App\Qyweixin\Models\Contact;

class BatchInvite extends \App\Common\Models\Qyweixin\Contact\BatchInvite
{

    public function recordResult($id, $res, $now)
    {
        /**
         * {
         * "errcode" : 0,
         * "errmsg" : "ok",
         * "invaliduser" : ["UserID1", "UserID2"],
         * "invalidparty" : [PartyID1, PartyID2],
         * "invalidtag": [TagID1, TagID2]
         * }
         */
        $updateData = array();
        $updateData['invite_time'] = \App\Common\Utils\Helper::getCurrentTime($now);
        $updateData['invaliduser'] = empty($res['invaliduser']) ? "[]" : \App\Common\Utils\Helper::myJsonEncode($res['invaliduser']);
        $updateData['invalidparty'] = empty($res['invalidparty']) ? "[]" : \App\Common\Utils\Helper::myJsonEncode($res['invalidparty']);
        $updateData['invalidtag'] = empty($res['invalidtag']) ? "[]" : \App\Common\Utils\Helper::myJsonEncode($res['invalidtag']);
        $updateData['memo'] = empty($res) ? "{}" : \App\Common\Utils\Helper::myJsonEncode($res);
        return $this->update(array('_id' => $id), array('$set' => $updateData));
    }
}
