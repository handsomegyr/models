<?php

namespace App\Common\Models\Weixin2\Mysql\Miniprogram;

use App\Common\Models\Base\Mysql\Base;

class Urlscheme extends Base
{
    /**
     * 微信-小程序scheme码
     * This model is mapped to the table iweixin2_miniprogram_urlscheme
     */
    public function getSource()
    {
        return 'iweixin2_miniprogram_urlscheme';
    }
    
    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);

        $data['expire_time'] = $this->changeToMongoDate($data['expire_time']);

        return $data;
    }
}
