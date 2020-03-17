<?php

namespace App\Weixin2\Models\User;

class SubscribeUser extends \App\Common\Models\Weixin2\User\SubscribeUser
{

    /**
     * 根据标签名获取信息
     *
     * @param string $openid            
     * @param string $authorizer_appid            
     * @param string $component_appid            
     */
    public function getInfoByOpenid($openid, $authorizer_appid, $component_appid)
    {
        $info = $this->findOne(array(
            'openid' => $openid,
            'authorizer_appid' => $authorizer_appid,
            'component_appid' => $component_appid
        ));
        return $info;
    }

    public function log($authorizer_appid, $component_appid, $openid, $now)
    {
        $data = array();
        $data['authorizer_appid'] = $authorizer_appid;
        $data['component_appid'] = $component_appid;
        $data['openid'] = $openid;
        $data['get_time'] = \App\Common\Utils\Helper::getCurrentTime($now);
        $this->insert($data);
    }
}
