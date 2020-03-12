<?php

namespace App\Weixin2\Models\DataCube;

class UpstreamMsgHour extends \App\Common\Models\Weixin2\DataCube\UpstreamMsgHour
{

    /**
     * 根据数据的日期获取信息
     *
     * @param string $ref_date            
     * @param number $ref_hour            
     * @param string $authorizer_appid            
     * @param string $component_appid            
     */
    public function getInfoByRefDateAndHour($ref_date, $authorizer_appid, $component_appid)
    {
        $info = $this->findOne(array(
            'ref_date' => $ref_date,
            'authorizer_appid' => $authorizer_appid,
            'component_appid' => $component_appid
        ));
        return $info;
    }

    public function syncUpstreamMsgHour($authorizer_appid, $component_appid, $res, $now)
    {
        if (!empty($res['list'])) {
            foreach ($res['list'] as $item) {
                $ref_date = $item['ref_date'] . " 00:00:00";
                $info = $this->getInfoByRefDateAndHour($ref_date, $item['ref_hour'], $authorizer_appid, $component_appid);
                $data = array();
                // [ref_date] => 2014-12-07
                // [ref_hour] => 0
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
                    $data['ref_date'] = $ref_date;
                    $data['ref_hour'] = $item['ref_hour'];
                    $this->insert($data);
                }
            }
        }
    }
}
