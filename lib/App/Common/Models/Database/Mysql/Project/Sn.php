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
        $data['active'] = $this->changeToBoolean($data['active']);
        $data['default'] = $this->changeToBoolean($data['default']);
        $data['expire'] = $this->changeToMongoDate($data['expire']);
        return $data;
    }
}
