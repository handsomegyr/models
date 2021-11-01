<?php

namespace App\Common\Models\Member\Mysql;

use App\Common\Models\Base\Mysql\Base;

class Tag extends Base
{

    /**
     * 会员-用户标签记录管理
     * This model is mapped to the table imember_tag
     */
    public function getSource()
    {
        return 'imember_tag';
    }
}
