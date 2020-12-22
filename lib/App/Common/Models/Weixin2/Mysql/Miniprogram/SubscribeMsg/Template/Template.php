<?php

namespace App\Common\Models\Weixin2\Mysql\Miniprogram\SubscribeMsg\Template;

use App\Common\Models\Base\Mysql\Base;

class Template extends Base
{

    /**
     * 微信-小程序订阅消息模板
     * This model is mapped to the table iweixin2_miniprogram_subscribemsg_template
     */
    public function getSource()
    {
        return 'iweixin2_miniprogram_subscribemsg_template';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);

        $data['template_time'] = $this->changeToMongoDate($data['template_time']);
        $data['is_created'] = $this->changeToBoolean($data['is_created']);
        return $data;
    }
}
