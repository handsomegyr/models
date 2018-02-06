<?php
namespace App\Common\Models\Live\Mysql;

use App\Common\Models\Base\Mysql\Base;

class User extends Base
{

    /**
     * 直播-用户管理
     * This model is mapped to the table ilive_user
     */
    public function getSource()
    {
        return 'ilive_user';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['is_auchor'] = $this->changeToBoolean($data['is_auchor']);
        $data['is_vip'] = $this->changeToBoolean($data['is_vip']);
        $data['is_test'] = $this->changeToBoolean($data['is_test']);
        return $data;
    }
}