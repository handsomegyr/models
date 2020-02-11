<?php

namespace App\Common\Models\Weixin2\Mysql;

use App\Common\Models\Base\Mysql\Base;

class Callbackurls extends Base
{
    /**
     * 微信-回调地址安全域名
     * This model is mapped to the table iweixin2_callbackurls
     */
    public function getSource()
    {
        return 'iweixin2_callbackurls';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['is_valid'] = $this->changeToBoolean($data['is_valid']);
        return $data;
    }
}
