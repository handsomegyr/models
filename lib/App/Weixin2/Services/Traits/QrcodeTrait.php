<?php

namespace App\Weixin2\Services\Traits;

trait QrcodeTrait
{
    /**
     * 创建二维码
     */
    public function createQrcode($qrcode_id)
    {
        $modelQrcode = new \App\Weixin2\Models\Qrcode\Qrcode();
        $qrcodeInfo = $modelQrcode->getInfoById($qrcode_id);
        if (empty($qrcodeInfo)) {
            throw new \Exception("二维码记录ID:{$qrcode_id}所对应的二维码不存在");
        }
        if (empty($qrcodeInfo['expire_seconds'])) {
            $qrcodeInfo['expire_seconds'] = 0;
        }

        // 如果是永久并且已生成的话
        if (in_array($qrcodeInfo['action_name'], array(
            "QR_LIMIT_SCENE",
            "QR_LIMIT_STR_SCENE"
        )) && !empty($qrcodeInfo['is_created'])) {
            throw new \Exception("二维码记录ID:{$qrcode_id}所对应的二维码是永久二维码并且已生成");
        }
        // 如果是临时并且已生成并且没有过期
        if (in_array($qrcodeInfo['action_name'], array(
            "QR_SCENE",
            "QR_STR_SCENE"
        )) && !empty($qrcodeInfo['is_created']) && (strtotime($qrcodeInfo['ticket_time']) + $qrcodeInfo['expire_seconds']) > time()) {
            throw new \Exception("二维码记录ID:{$qrcode_id}所对应的二维码是临时二维码并且已生成并且没有过期");
        }

        $qrcodeManager = $this->getWeixinObject()->getQrcodeManager();
        $res = $qrcodeManager->create3($qrcodeInfo['action_name'], $qrcodeInfo['scene'], $qrcodeInfo['expire_seconds']);
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        $ticket = \urlencode($res['ticket']);
        $ticket = $qrcodeManager->getQrcodeUrl($ticket);

        $modelQrcode->recordTicket($qrcode_id, $ticket, $res, time());
        return $res;
    }
}
