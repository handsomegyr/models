<?php
namespace App\Common\Models\Alipay\Mysql;

use App\Common\Models\Base\Mysql\Base;

class Callbackurls extends Base
{

    /**
     * 支付宝-回调地址安全域名管理
     * This model is mapped to the table ialipay_callbackurls
     */
    public function getSource()
    {
        return 'ialipay_callbackurls';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['is_valid'] = $this->changeToBoolean($data['is_valid']);
        return $data;
    }
}