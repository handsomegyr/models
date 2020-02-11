<?php

namespace App\Common\Models\Weixin2\Mysql\DataCube;

use App\Common\Models\Base\Mysql\Base;

class UpstreamMsgMonth extends Base
{

    /**
     * 微信-消息发送月数据
     * This model is mapped to the table iweixin2_datacube_upstreammsgmonth
     */
    public function getSource()
    {
        return 'iweixin2_datacube_upstreammsgmonth';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);

        $data['ref_date'] = $this->changeToMongoDate($data['ref_date']);
        return $data;
    }
}
