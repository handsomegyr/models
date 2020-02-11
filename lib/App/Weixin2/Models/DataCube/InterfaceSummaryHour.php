<?php

namespace App\Weixin2\Models\DataCube;

class InterfaceSummaryHour extends \App\Common\Models\Weixin2\DataCube\InterfaceSummaryHour
{

    /**
     * 根据数据的日期和小时获取信息
     *
     * @param string $ref_date            
     * @param number $ref_hour            
     * @param string $authorizer_appid            
     * @param string $component_appid            
     */
    public function getInfoByRefDateAndHour($ref_date, $ref_hour, $authorizer_appid, $component_appid)
    {
        $info = $this->getModel()
            ->where('ref_date', $ref_date)
            ->where('ref_hour', $ref_hour)
            ->where('authorizer_appid', $authorizer_appid)
            ->where('component_appid', $component_appid)
            ->first();
        $info = $this->getReturnData($info);

        return $info;
    }

    public function syncInterfaceSummaryHour($authorizer_appid, $component_appid, $res, $now)
    {
        if (!empty($res['list'])) {
            foreach ($res['list'] as $item) {
                $ref_date = $item['ref_date'] . " 00:00:00";
                $info = $this->getInfoByRefDateAndHour($ref_date, $item['ref_hour'], $authorizer_appid, $component_appid);
                $data = array();
                // [ref_date] => 2014-12-07
                // [ref_hour] => 0
                // [callback_count] => 0
                // [fail_count] => 1
                // [total_time_cost] => 1
                // [max_time_cost] => 1
                $data['callback_count'] = $item['callback_count'];
                $data['fail_count'] = $item['fail_count'];
                $data['total_time_cost'] = $item['total_time_cost'];
                $data['max_time_cost'] = $item['max_time_cost'];

                if (!empty($info)) {
                    $this->updateById($info['id'], $data);
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
