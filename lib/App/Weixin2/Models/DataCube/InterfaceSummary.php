<?php

namespace App\Weixin2\Models\DataCube;

class InterfaceSummary extends \App\Common\Models\Weixin2\DataCube\InterfaceSummary
{

    /**
     * 根据数据的日期获取信息
     *
     * @param string $ref_date            
     * @param string $authorizer_appid            
     * @param string $component_appid           
     */
    public function getInfoByRefDate($ref_date, $authorizer_appid, $component_appid)
    {
        $info = $this->findOne(array(
            'ref_date' => $ref_date,
            'authorizer_appid' => $authorizer_appid,
            'component_appid' => $component_appid
        ));
        return $info;
    }

    public function syncInterfaceSummary($authorizer_appid, $component_appid, $res, $now)
    {
        if (!empty($res['list'])) {
            foreach ($res['list'] as $item) {
                $ref_date = $item['ref_date'] . " 00:00:00";
                $info = $this->getInfoByRefDate($ref_date, $authorizer_appid, $component_appid);
                $data = array();
                // [ref_date] => 2014-12-07
                // [callback_count] => 0
                // [fail_count] => 1
                // [total_time_cost] => 1
                // [max_time_cost] => 1
                $data['callback_count'] = $item['callback_count'];
                $data['fail_count'] = $item['fail_count'];
                $data['total_time_cost'] = $item['total_time_cost'];
                $data['max_time_cost'] = $item['max_time_cost'];

                if (!empty($info)) {
                    $this->update(array('_id' => $info['_id']), array('$set' => $data));
                } else {
                    $data['authorizer_appid'] = $authorizer_appid;
                    $data['component_appid'] = $component_appid;
                    $data['ref_date'] = $ref_date;
                    $this->insert($data);
                }
            }
        }
    }
}
