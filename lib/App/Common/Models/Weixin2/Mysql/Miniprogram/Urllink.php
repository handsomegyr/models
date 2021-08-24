<?php

namespace App\Common\Models\Weixin2\Mysql\Miniprogram;

use App\Common\Models\Base\Mysql\Base;

class Urllink extends Base
{
    /**
     * 微信-小程序URL链接
     * This model is mapped to the table iweixin2_miniprogram_urllink
     */
    public function getSource()
    {
        return 'iweixin2_miniprogram_urllink';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);

        $data['expire_time'] = $this->changeToValidDate($data['expire_time']);

        return $data;
    }
}
