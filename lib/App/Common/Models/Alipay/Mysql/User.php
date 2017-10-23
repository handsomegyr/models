<?php
namespace App\Common\Models\Alipay\Mysql;

use App\Common\Models\Base\Mysql\Base;

class User extends Base
{

    /**
     * 支付宝-用户管理
     * This model is mapped to the table ialipay_user
     */
    public function getSource()
    {
        return 'ialipay_user';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['access_token'] = $this->changeToArray($data['access_token']);
        return $data;
    }
}