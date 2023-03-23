<?php

namespace App\Common\Models\Weixin2\Mysql;

use App\Common\Models\Base\Mysql\Base;

class SnsApplication extends Base
{
    /**
     * 微信-授权应用
     * This model is mapped to the table iweixin2_sns_application
     */
    public function getSource()
    {
        return 'iweixin2_sns_application';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);

        $data['start_time'] = $this->changeToValidDate($data['start_time']);
        $data['end_time'] = $this->changeToValidDate($data['end_time']);

        $data['is_active'] = $this->changeToBoolean($data['is_active']);
        $data['is_ip_check'] = $this->changeToBoolean($data['is_ip_check']);
        $data['is_cb_url_check'] = $this->changeToBoolean($data['is_cb_url_check']);

        $data['ip_list'] = $this->changeToArray($data['ip_list']);
        $data['cb_url_list'] = $this->changeToArray($data['cb_url_list']);
        return $data;
    }
}
