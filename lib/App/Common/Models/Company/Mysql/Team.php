<?php

namespace App\Common\Models\Company\Mysql;

use App\Common\Models\Base\Mysql\Base;

class Team extends Base
{

    /**
     * 公司-团队管理
     * This model is mapped to the table icompany_team
     */
    public function getSource()
    {
        return 'icompany_team';
    }
}
