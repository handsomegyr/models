<?php

namespace App\Common\Models\Qyweixin\Mysql\Provider;

use App\Common\Models\Base\Mysql\Base;

class Provider extends Base
{
    /**
     * 企业微信-第三方平台应用设置
     * This model is mapped to the table iqyweixin_provider
     */
    public function getSource()
    {
        return 'iqyweixin_provider';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);

        $data['access_token_expire'] = $this->changeToValidDate($data['access_token_expire']);
        return $data;
    }
}
