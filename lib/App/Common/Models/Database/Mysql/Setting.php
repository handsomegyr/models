<?php

namespace App\Common\Models\Database\Mysql;

use App\Common\Models\Base\Mysql\Base;

class Setting extends Base
{

    /**
     * 数据库-参数设置表管理
     * This model is mapped to the table idatabase_setting
     */
    public function getSource()
    {
        return 'idatabase_setting';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['is_auth'] = $this->changeToBoolean($data['is_auth']);
        $data['is_smtp'] = $this->changeToBoolean($data['is_smtp']);
        return $data;
    }
}
