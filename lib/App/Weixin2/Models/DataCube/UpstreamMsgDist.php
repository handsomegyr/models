<?php

namespace App\Weixin2\Models\DataCube;

class UpstreamMsgDist extends \App\Common\Models\Weixin2\DataCube\UpstreamMsgDist
{

    /**
     * 根据数据的日期获取信息
     *
     * @param string $ref_date            
     * @param string $authorizer_appid            
     * @param string $component_appid             
     * @param string $agentid            
     */
    public function getInfoByRefDate($ref_date, $authorizer_appid, $component_appid, $agentid)
    {
        $info = $this->findOne(array(
            'ref_date' => $ref_date,
            'authorizer_appid' => $authorizer_appid,
            'component_appid' => $component_appid,
            'agentid' => $agentid,
        ));
        return $info;
    }

    public function syncUpstreamMsgDist($authorizer_appid, $component_appid, $agentid, $res, $now)
    {
        if (!empty($res['list'])) {
            foreach ($res['list'] as $item) {
                $ref_date = $item['ref_date'] . " 00:00:00";
                $info = $this->getInfoByRefDate($ref_date, $authorizer_appid, $component_appid, $agentid);
                $data = array();
                // [ref_date] => 2014-12-07
                // [count_interval] => 0
                // [msg_user] => 1
                $data['count_interval'] = $item['count_interval'];
                $data['msg_user'] = $item['msg_user'];
                if (!empty($info)) {
                    $this->update(array('_id' => $info['_id']), array('$set' => $data));
                } else {
                    $data['authorizer_appid'] = $authorizer_appid;
                    $data['component_appid'] = $component_appid;
                    $data['agentid'] = $agentid;
                    $data['ref_date'] = $ref_date;
                    $this->insert($data);
                }
            }
        }
    }
}
