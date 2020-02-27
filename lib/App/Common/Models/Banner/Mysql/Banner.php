<?php

namespace App\Common\Models\Banner\Mysql;

use App\Common\Models\Base\Mysql\Base;

class Banner extends Base
{

    /**
     * banner-banner管理
     * This model is mapped to the table ibanner_banner
     */
    public function getSource()
    {
        return 'ibanner_banner';
    }
}
