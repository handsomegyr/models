<?php

namespace App\Common\Models\Database\Mysql\Project;

use App\Common\Models\Base\Mysql\Base;

class Sn extends Base
{

    /**
     * 数据库-数据库密钥管理
     * This model is mapped to the table idatabase_project_sn
     */
    public function getSource()
    {
        return 'idatabase_project_sn';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['is_actived'] = $this->changeToBoolean($data['is_actived']);
        $data['is_default'] = $this->changeToBoolean($data['is_default']);
        $data['expire_time'] = $this->changeToValidDate($data['expire_time']);
        return $data;
    }
}
