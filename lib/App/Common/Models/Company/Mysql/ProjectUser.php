<?php

namespace App\Common\Models\Company\Mysql;

use App\Common\Models\Base\Mysql\Base;

class ProjectUser extends Base
{

    /**
     * 公司-项目用户管理
     * This model is mapped to the table icompany_project_user
     */
    public function getSource()
    {
        return 'icompany_project_user';
    }
}
