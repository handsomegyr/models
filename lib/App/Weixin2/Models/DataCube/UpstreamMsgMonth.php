<?php

namespace App\Weixin2\Models\DataCube;

class UpstreamMsgMonth extends \App\Common\Models\Weixin2\DataCube\UpstreamMsgMonth
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

    public function syncUpstreamMsgMonth($authorizer_appid, $component_appid, $agentid, $res, $now)
    {
        if (!empty($res['list'])) {
            foreach ($res['list'] as $item) {
                $ref_date = $item['ref_date'] . " 00:00:00";
                $info = $this->getInfoByRefDate($ref_date, $authorizer_appid, $component_appid, $agentid);
                $data = array();
                // [ref_date] => 2014-12-07
                // [msg_type] => 1
                // [msg_user] => 282
                // [msg_count] => 817
                $data['msg_type'] = $item['msg_type'];
                $data['msg_user'] = $item['msg_user'];
                $data['msg_count'] = $item['msg_count'];

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
