<?php

namespace App\Weixin2\Models\Qrcode;

class Qrcode extends \App\Common\Models\Weixin2\Qrcode\Qrcode
{

    public function recordTicket($id, $ticket, $res, $now)
    {
        $updateData = array();
        $updateData['ticket'] = $ticket;
        $updateData['url'] = $res['url'];
        $updateData['ticket_time'] = getCurrentTime($now);
        $updateData['is_created'] = 1;
        return $this->update(array('_id' => $id), array('$set' => $updateData));
    }

    public function incSubscribeEventNum($authorizer_appid, $component_appid, $scene, $num = 1)
    {
        if (empty($num)) {
            return 0;
        }
        $incData = array();
        $incData['subscribe_event_num'] = $num;
        $query = array(
            'scene' => $scene,
            'authorizer_appid' => $authorizer_appid,
            'component_appid' => $component_appid
        );
        $affectRows = $this->update($query, array('$inc' => $incData));
        return $affectRows;
    }

    public function incScanEventNum($authorizer_appid, $component_appid, $scene, $num = 1)
    {
        if (empty($num)) {
            return 0;
        }
        $incData = array();
        $incData['scan_event_num'] = $num;

        $query = array(
            'scene' => $scene,
            'authorizer_appid' => $authorizer_appid,
            'component_appid' => $component_appid
        );
        $affectRows = $this->update($query, array('$inc' => $incData));
        return $affectRows;
    }
}
