<?php

namespace App\Weixin2\Services\Traits\Miniprogram;

trait QrcodeTrait
{
    // 创建小程序二维码
    public function createMiniappQrcode($qrcode_id, $is_auto = 0, $channel = '', $name = '')
    {
        $modelQrcode = new \App\Weixin2\Models\Miniprogram\Qrcode\Qrcode();
        $qrcodeInfo = $modelQrcode->getInfoById($qrcode_id);
        if (empty($qrcodeInfo)) {
            throw new \Exception("小程序二维码记录ID:{$qrcode_id}所对应的二维码不存在");
        }
        $scene = $qrcodeInfo['scene'];
        $page = $qrcodeInfo['pagepath'];
        $path = $qrcodeInfo['path'];
        $width = $qrcodeInfo['width'];
        if (empty($width)) {
            $width = 430;
        }
        $auto_color = (intval($qrcodeInfo['auto_color']) === 0) ? false : true;
        $line_color = $qrcodeInfo['line_color'];
        if (empty($line_color)) {
            $line_color = array("r" => "0", "g" => "0", "b" => "0");
        }
        $is_hyaline = (intval($qrcodeInfo['is_hyaline']) === 0) ? false : true;

        $qrCodeManager = $this->getWeixinObject()->getWxClient()->getQrcodeManager();
        switch ($qrcodeInfo['type']) {
            case "getwxacode": //接口A
                $res = $qrCodeManager->getwxacode2($path, $width, $auto_color, $line_color, $is_hyaline);
                if (!empty($res['errcode'])) {
                    throw new \Exception($res['errmsg'], $res['errcode']);
                }
                $content = $res['wxacode'];
                break;
            case "getwxacodeunlimit": //接口B
                $res = $qrCodeManager->getwxacodeunlimit2($scene, $page, $width, $auto_color, $line_color, $is_hyaline);
                if (!empty($res['errcode'])) {
                    throw new \Exception($res['errmsg'], $res['errcode']);
                }
                $content = $res['wxacode'];
                break;
            case "createwxaqrcode": //接口C
                $res = $qrCodeManager->createwxaqrcode($path, $width);
                if (empty($res)) {
                    throw new \Exception('生成二维码失败');
                }
                $content = $res;
                break;
        }
        $fileName = md5(time() . uniqid() . '_' . $qrcodeInfo['type']) . '.jpg';
        $path = 'upload/miniappqr/' . $fileName;
        $r = file_put_contents(APP_PATH . '/public/' . $path, $content); // 返回的是字节数
        if (!$r) {
            throw new \Exception('保存文件失败');
        }
        $modelQrcode->recordQrcode($qrcode_id, $path, time(), $is_auto, $channel, $name);

        return $path;
    }
}
