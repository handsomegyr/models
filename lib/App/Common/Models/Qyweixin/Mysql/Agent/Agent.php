<?php

namespace App\Common\Models\Qyweixin\Mysql\Agent;

use App\Common\Models\Base\Mysql\Base;

class Agent extends Base
{
    /**
     * 企业微信-应用
     * This model is mapped to the table iqyweixin_agent
     */
    public function getSource()
    {
        return 'iqyweixin_agent';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);

        $data['access_token_expire'] = $this->changeToMongoDate($data['access_token_expire']);
        $data['jsapi_ticket_expire'] = $this->changeToMongoDate($data['jsapi_ticket_expire']);

        $data['logo_media_created_at'] = $this->changeToMongoDate($data['logo_media_created_at']);
        $data['allow_userinfos'] = $this->changeToArray($data['allow_userinfos']);
        $data['allow_partys'] = $this->changeToArray($data['allow_partys']);
        $data['allow_tags'] = $this->changeToArray($data['allow_tags']);

        $data['close'] = $this->changeToBoolean($data['close']);
        $data['report_location_flag'] = $this->changeToBoolean($data['report_location_flag']);
        $data['isreportenter'] = $this->changeToBoolean($data['isreportenter']);

        $data['sync_time'] = $this->changeToMongoDate($data['sync_time']);
        return $data;
    }
}
