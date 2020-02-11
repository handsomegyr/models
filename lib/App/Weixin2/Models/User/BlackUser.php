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
        $info = $this->getModel()
            ->where('openid', $openid)
            ->where('authorizer_appid', $authorizer_appid)
            ->where('component_appid', $component_appid)
            ->first();
        $info = $this->getReturnData($info);

        return $info;
    }

    public function black($id, $res, $now)
    {
        $updateData = array();
        $updateData['is_black'] = 1;
        $updateData['black_time'] = date("Y-m-d H:i:s", $now);
        return $this->updateById($id, $updateData);
    }

    public function unblack($id, $now)
    {
        $updateData = array();
        $updateData['is_black'] = 0;
        $updateData['unblack_time'] = date("Y-m-d H:i:s", $now);
        return $this->updateById($id, $updateData);
    }

    public function syncBlackList($authorizer_appid, $component_appid, $res, $now)
    {
        if (!empty($res['data']['openid'])) {
            foreach ($res['data']['openid'] as $openid) {
                $info = $this->getInfoByOpenid($openid, $authorizer_appid, $component_appid);
                $data = array();
                $data['is_black'] = 1;
                $data['black_time'] = date("Y-m-d H:i:s", $now);
                if (!empty($info)) {
                    $this->updateById($info['id'], $data);
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
