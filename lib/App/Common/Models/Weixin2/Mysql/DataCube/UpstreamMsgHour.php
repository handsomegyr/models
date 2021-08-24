<?php

namespace App\Common\Models\Weixin2\Mysql\DataCube;

use App\Common\Models\Base\Mysql\Base;

class UpstreamMsgHour extends Base
{

    /**
     * 微信-消息发送分时数据
     * This model is mapped to the table iweixin2_datacube_upstreammsghour
     */
    public function getSource()
    {
        return 'iweixin2_datacube_upstreammsghour';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);

        $data['ref_date'] = $this->changeToValidDate($data['ref_date']);
        return $data;
    }
}
