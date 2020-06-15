<?php

namespace App\Common\Models\Database\Mysql;

use App\Common\Models\Base\Mysql\Base;

class Statistic extends Base
{

    /**
     * 数据库-统计管理
     * This model is mapped to the table idatabase_statistic
     */
    public function getSource()
    {
        return 'idatabase_statistic';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);

        $data['defaultQuery'] = $this->changeToArray($data['defaultQuery']);
        $data['dashboardQuery'] = $this->changeToArray($data['dashboardQuery']);

        $data['lastExecuteTime'] = $this->changeToMongoDate($data['lastExecuteTime']);
        $data['resultExpireTime'] = $this->changeToMongoDate($data['resultExpireTime']);

        $data['isDashboard'] = $this->changeToBoolean($data['isDashboard']);
        $data['colspan'] = $this->changeToBoolean($data['colspan']);
        $data['isRunning'] = $this->changeToBoolean($data['isRunning']);
        return $data;
    }
}
