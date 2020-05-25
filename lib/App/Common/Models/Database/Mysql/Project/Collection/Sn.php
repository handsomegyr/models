<?php

namespace App\Common\Models\Database\Mysql\Project\Collection;

use App\Common\Models\Base\Mysql\Base;

class Sn extends Base
{

    /**
     * 数据库-数据库表密钥管理
     * This model is mapped to the table idatabase_project_collection_sn
     */
    public function getSource()
    {
        return 'idatabase_project_collection_sn';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['is_actived'] = $this->changeToBoolean($data['is_actived']);
        return $data;
    }
}
