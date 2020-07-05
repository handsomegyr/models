<?php

namespace App\Qyweixin\Models\Contact;

class UserActiveStat extends \App\Common\Models\Qyweixin\Contact\UserActiveStat
{

    /**
     * 根据day获取信息
     *
     * @param string $day           
     * @param string $authorizer_appid          
     */
    public function getInfoByDay($day, $authorizer_appid)
    {
        $query = array();
        $query['day'] = $day;
        $query['authorizer_appid'] = $authorizer_appid;
        $info = $this->findOne($query);

        return $info;
    }

    public function syncActiveStat($day, $authorizer_appid, $provider_appid, $res, $now)
    {
        $info = $this->getInfoByDay($day, $authorizer_appid);
        $data = array();
        $data['provider_appid'] = $provider_appid;
        $data['get_time'] = \App\Common\Utils\Helper::getCurrentTime($now);
        $data['active_cnt'] = $res['active_cnt'];
        if (!empty($info)) {
            $this->update(array('_id' => $info['_id']), array('$set' => $data));
        } else {
            $data['authorizer_appid'] = $authorizer_appid;
            $data['day'] = $day;
            $this->insert($data);
        }
    }
}
