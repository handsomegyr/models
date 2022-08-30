<?php

namespace App\Qyweixin\Models\Contact;

class UserActiveStat extends \App\Common\Models\Qyweixin\Contact\UserActiveStat
{

    /**
     * 根据day获取信息
     *
     * @param string $day
     * @param string $agentid
     * @param string $authorizer_appid
     * @param string $provider_appid
     */
    public function getInfoByDay($day, $agentid, $authorizer_appid, $provider_appid)
    {
        $query = array();
        $query['day'] = $day;
        $query['agentid'] = $agentid;
        $query['authorizer_appid'] = $authorizer_appid;
        $query['provider_appid'] = $provider_appid;
        $info = $this->findOne($query);

        return $info;
    }

    public function syncActiveStat($day, $agentid, $authorizer_appid, $provider_appid, $res, $now)
    {
        $info = $this->getInfoByDay($day, $agentid, $authorizer_appid, $provider_appid);
        $data = array();
        $data['get_time'] = \App\Common\Utils\Helper::getCurrentTime($now);
        $data['active_cnt'] = $res['active_cnt'];
        if (!empty($info)) {
            $this->update(array('_id' => $info['_id']), array('$set' => $data));
        } else {
            $data['provider_appid'] = $provider_appid;
            $data['authorizer_appid'] = $authorizer_appid;
            $data['agentid'] = $agentid;
            $data['day'] = $day;
            $this->insert($data);
        }
    }
}
