<?php

namespace App\Weixin2\Models\SubscribeMsg;

class SubscribeLog extends \App\Common\Models\Weixin2\SubscribeMsg\SubscribeLog
{

    public function getInfoByOpenidAndTemplateIdAndScene($openid, $template_id, $scene, $authorizer_appid, $component_appid)
    {
        $info = $this->findOne(array(
            'openid' => $openid,
            'scene' => $scene,
            'template_id' => $template_id,
            'authorizer_appid' => $authorizer_appid,
            'component_appid' => $component_appid
        ));
        return $info;
    }

    public function log($component_appid, $authorizer_appid, $appid, $openid, $template_id, $action, $scene, $reserved, $subscribe_time)
    {
        $data = array();
        $data['component_appid'] = $component_appid;
        $data['authorizer_appid'] = $authorizer_appid;
        $data['appid'] = $appid;
        $data['openid'] = $openid;
        $data['template_id'] = $template_id;
        $data['scene'] = $scene;
        $data['action'] = $action;
        $data['reserved'] = $reserved;
        $data['subscribe_time'] = date('Y-m-d H:i:s', $subscribe_time);
        $data['is_used'] = 0;
        return $this->insert($data);
    }
}
