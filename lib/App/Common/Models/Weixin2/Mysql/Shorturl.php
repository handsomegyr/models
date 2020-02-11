<?php

namespace App\Common\Models\Weixin2\Mysql;

use App\Common\Models\Base\Mysql\Base;

class Shorturl extends Base
{
    /**
     * 微信-短链接
     * This model is mapped to the table iweixin2_shorturl
     */
    public function getSource()
    {
        return 'iweixin2_shorturl';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);

        $data['short_url_time'] = $this->changeToMongoDate($data['short_url_time']);

        $data['is_created'] = $this->changeToBoolean($data['is_created']);
        return $data;
    }
}
