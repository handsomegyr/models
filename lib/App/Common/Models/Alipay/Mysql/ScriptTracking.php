<?php
namespace App\Common\Models\Alipay\Mysql;

use App\Common\Models\Base\Mysql\Base;

class ScriptTracking extends Base
{

    /**
     * 支付宝-授权执行时间跟踪统计管理
     * This model is mapped to the table ialipay_script_tracking
     */
    public function getSource()
    {
        return 'ialipay_script_tracking';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        return $data;
    }
}