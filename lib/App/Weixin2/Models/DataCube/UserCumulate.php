<?php

namespace App\Weixin2\Models\DataCube;

class UserCumulate extends \App\Common\Models\Weixin2\DataCube\UserCumulate
{

    /**
     * 根据数据的日期获取信息
     *
     * @param string $ref_date            
     * @param number $user_source            
     * @param string $authorizer_appid            
     * @param string $component_appid            
     */
    public function getInfoByRefDate($ref_date, $user_source, $authorizer_appid, $component_appid)
    {
        $info = $this->getModel()
            ->where('ref_date', $ref_date)
            ->where('user_source', $user_source)
            ->where('authorizer_appid', $authorizer_appid)
            ->where('component_appid', $component_appid)
            ->first();
        $info = $this->getReturnData($info);

        return $info;
    }

    public function syncUserCumulate($authorizer_appid, $component_appid, $res, $now)
    {
        if (!empty($res['list'])) {
            foreach ($res['list'] as $item) {
                $ref_date = $item['ref_date'] . " 00:00:00";
                $info = $this->getInfoByRefDate($ref_date, $item['user_source'], $authorizer_appid, $component_appid);
                $data = array();
                // [ref_date] => 2014-12-07
                // [cumulate_user] => 1
                $data['cumulate_user'] = $item['cumulate_user'];

                if (!empty($info)) {
                    $this->updateById($info['id'], $data);
                } else {
                    $data['authorizer_appid'] = $authorizer_appid;
                    $data['component_appid'] = $component_appid;
                    $data['ref_date'] = $ref_date;
                    $data['user_source'] = $item['user_source'];
                    $this->insert($data);
                }
            }
        }
    }
}
