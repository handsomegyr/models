<?php

namespace App\Weixin2\Models\DataCube;

class UserRead extends \App\Common\Models\Weixin2\DataCube\UserRead
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

    public function syncUserRead($authorizer_appid, $component_appid, $res, $now)
    {
        if (!empty($res['list'])) {
            foreach ($res['list'] as $item) {
                $ref_date = $item['ref_date'] . " 00:00:00";
                $info = $this->getInfoByRefDate($ref_date, $authorizer_appid, $component_appid);
                $data = array();
                // [ref_date] => 2014-12-07
                // [user_source] => 0
                // [new_user] => 1
                // [cancel_user] => 1
                // [cumulate_user] => 1
                $data['user_source'] = $item['user_source'];
                $data['new_user'] = $item['new_user'];
                $data['cancel_user'] = $item['cancel_user'];
                $data['cumulate_user'] = empty($item['cumulate_user']) ? 0 : $item['cumulate_user'];

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
