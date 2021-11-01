<?php

namespace App\Common\Models\Tag\Mysql;

use App\Common\Models\Base\Mysql\Base;

class Category extends Base
{

    /**
     * 标签-标签分类管理
     * This model is mapped to the table itag_category
     */
    public function getSource()
    {
        return 'itag_category';
    }
}
