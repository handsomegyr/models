<?php

namespace App\Common\Models\Company\Mysql;

use App\Common\Models\Base\Mysql\Base;

class TeamUserWork extends Base
{

    /**
     * 公司-团队用户工作管理
     * This model is mapped to the table icompany_team_user_work
     */
    public function getSource()
    {
        return 'icompany_team_user_work';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['schedule_start_time'] = $this->changeToMongoDate($data['schedule_start_time']);
        $data['schedule_end_time'] = $this->changeToMongoDate($data['schedule_end_time']);
        $data['work_complete_time'] = $this->changeToMongoDate($data['work_complete_time']);
        return $data;
    }
}
