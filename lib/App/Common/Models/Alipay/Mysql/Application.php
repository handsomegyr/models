<?php
namespace App\Common\Models\Alipay\Mysql;

use App\Common\Models\Base\Mysql\Base;

class Application extends Base
{

    /**
     * 支付宝-应用设置管理
     * This model is mapped to the table ialipay_application
     */
    public function getSource()
    {
        return 'ialipay_application';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        return $data;
    }
}