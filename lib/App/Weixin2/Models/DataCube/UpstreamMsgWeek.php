<?php

namespace App\Weixin2\Models\DataCube;

class UpstreamMsgWeek extends \App\Common\Models\Weixin2\DataCube\UpstreamMsgWeek
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
        $info = $this->getModel()
            ->where('ref_date', $ref_date)
            ->where('authorizer_appid', $authorizer_appid)
            ->where('component_appid', $component_appid)
            ->first();
        $info = $this->getReturnData($info);

        return $info;
    }

    public function syncUpstreamMsgWeek($authorizer_appid, $component_appid, $res, $now)
    {
        if (!empty($res['list'])) {
            foreach ($res['list'] as $item) {
                $ref_date = $item['ref_date'] . " 00:00:00";
                $info = $this->getInfoByRefDate($ref_date, $authorizer_appid, $component_appid);
                $data = array();
                // [ref_date] => 2014-12-07
                // [msg_type] => 1
                // [msg_user] => 282
                // [msg_count] => 817
                $data['msg_type'] = $item['msg_type'];
                $data['msg_user'] = $item['msg_user'];
                $data['msg_count'] = $item['msg_count'];

                if (!empty($info)) {
                    $this->updateById($info['id'], $data);
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
