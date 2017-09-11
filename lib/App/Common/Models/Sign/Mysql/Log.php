<?php
namespace App\Common\Models\Sign\Mysql;
use App\Common\Models\Base\Mysql\Base;
class Log extends Base
{
    /**
     * 签到-签到日志管理
     * This model is mapped to the table isign_log
     */
    public function getSource()
    {
        return 'isign_log';
    }
    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['sign_time'] = $this->changeToMongoDate($data['sign_time']);
        return $data;
    }
}