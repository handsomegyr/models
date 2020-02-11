<?php

namespace App\Weixin2\Models\Qrcode;

use DB;

class Qrcode extends \App\Common\Models\Weixin2\Qrcode\Qrcode
{

    public function recordTicket($id, $ticket, $res, $now)
    {
        $updateData = array();
        $updateData['ticket'] = $ticket;
        $updateData['url'] = $res['url'];
        $updateData['ticket_time'] = date("Y-m-d H:i:s", $now);
        $updateData['is_created'] = 1;
        return $this->updateById($id, $updateData);
    }

    public function incSubscribeEventNum($authorizer_appid, $component_appid, $scene, $num = 1)
    {
        $updateData = array();

        if ($num != 0) {
            if ($num > 0) {
                $num = abs($num);
                $updateData['subscribe_event_num'] = DB::raw("subscribe_event_num+{$num}");
            } else {
                $num = abs($num);
                $updateData['subscribe_event_num'] = DB::raw("subscribe_event_num-{$num}");
            }
        }
        $affectRows = 0;
        if (!empty($updateData)) {
            $updateModel = $this->getModel()
                ->where("scene", $scene)
                ->where("authorizer_appid", $authorizer_appid)
                ->where("component_appid", $component_appid);
            $affectRows = $this->update($updateModel, $updateData);
        }
        return $affectRows;
    }

    public function incScanEventNum($authorizer_appid, $component_appid, $scene, $num = 1)
    {
        $updateData = array();

        if ($num != 0) {
            if ($num > 0) {
                $num = abs($num);
                $updateData['scan_event_num'] = DB::raw("scan_event_num+{$num}");
            } else {
                $num = abs($num);
                $updateData['scan_event_num'] = DB::raw("scan_event_num-{$num}");
            }
        }
        $affectRows = 0;
        if (!empty($updateData)) {
            $updateModel = $this->getModel()
                ->where("scene", $scene)
                ->where("authorizer_appid", $authorizer_appid)
                ->where("component_appid", $component_appid);
            $affectRows = $this->update($updateModel, $updateData);
        }
        return $affectRows;
    }
}
