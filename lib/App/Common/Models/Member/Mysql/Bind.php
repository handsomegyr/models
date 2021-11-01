<?php

namespace App\Common\Models\Member\Mysql;

use App\Common\Models\Base\Mysql\Base;

class Bind extends Base
{

    /**
     * 会员-绑定管理
     * This model is mapped to the table imember_bind
     */
    public function getSource()
    {
        return 'imember_bind';
    }
}
