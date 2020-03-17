<?php

namespace App\Weixin2\Models\User;

class BlackUser extends \App\Common\Models\Weixin2\User\BlackUser
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

    public function black($id, $res, $now)
    {
        $updateData = array();
        $updateData['is_black'] = 1;
        $updateData['black_time'] = \App\Common\Utils\Helper::getCurrentTime($now);
        return $this->update(array('_id' => $id), array('$set' => $updateData));
    }

    public function unblack($id, $now)
    {
        $updateData = array();
        $updateData['is_black'] = 0;
        $updateData['unblack_time'] = \App\Common\Utils\Helper::getCurrentTime($now);
        return $this->update(array('_id' => $id), array('$set' => $updateData));
    }

    public function syncBlackList($authorizer_appid, $component_appid, $res, $now)
    {
        if (!empty($res['data']['openid'])) {
            foreach ($res['data']['openid'] as $openid) {
                $info = $this->getInfoByOpenid($openid, $authorizer_appid, $component_appid);
                $data = array();
                $data['is_black'] = 1;
                $data['black_time'] = \App\Common\Utils\Helper::getCurrentTime($now);
                if (!empty($info)) {
                    $this->update(array('_id' => $info['_id']), array('$set' => $data));
                } else {
                    $data['authorizer_appid'] = $authorizer_appid;
                    $data['component_appid'] = $component_appid;
                    $data['openid'] = $openid;
                    $this->insert($data);
                }
            }
        }
    }
}
