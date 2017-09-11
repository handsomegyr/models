<?php
namespace App\Common\Models\Sign\Mysql;
use App\Common\Models\Base\Mysql\Base;
class Sign extends Base
{
    /**
     * 签到-用户签到管理
     * This model is mapped to the table isign_sign
     */
    public function getSource()
    {
        return 'isign_sign';
    }
    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['first_sign_time'] = $this->changeToMongoDate($data['first_sign_time']);
        $data['restart_sign_time'] = $this->changeToMongoDate($data['restart_sign_time']);
        $data['last_sign_time'] = $this->changeToMongoDate($data['last_sign_time']);
        $data['is_continue_sign'] = $this->changeToBoolean($data['is_continue_sign']);
        $data['is_do'] = $this->changeToBoolean($data['is_do']);
        return $data;
    }
}