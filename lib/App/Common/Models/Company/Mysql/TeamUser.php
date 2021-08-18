<?php

namespace App\Common\Models\Company\Mysql;

use App\Common\Models\Base\Mysql\Base;

class TeamUser extends Base
{

    /**
     * 公司-团队用户管理
     * This model is mapped to the table icompany_team_user
     */
    public function getSource()
    {
        return 'icompany_team_user';
    }
    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['is_team_manager'] = $this->changeToBoolean($data['is_team_manager']);
        return $data;
    }
}
