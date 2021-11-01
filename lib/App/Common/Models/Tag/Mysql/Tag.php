<?php

namespace App\Common\Models\Tag\Mysql;

use App\Common\Models\Base\Mysql\Base;

class Tag extends Base
{

    /**
     * 标签-标签管理
     * This model is mapped to the table itag_tag
     */
    public function getSource()
    {
        return 'itag_tag';
    }
}
