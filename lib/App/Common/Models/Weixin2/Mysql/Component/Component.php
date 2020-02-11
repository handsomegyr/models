<?php

namespace App\Common\Models\Weixin2\Mysql\Component;

use App\Common\Models\Base\Mysql\Base;

class Component extends Base
{
    /**
     * 微信-第三方平台应用设置
     * This model is mapped to the table iweixin2_component
     */
    public function getSource()
    {
        return 'iweixin2_component';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);

        $data['access_token_expire'] = $this->changeToMongoDate($data['access_token_expire']);
        return $data;
    }
}
