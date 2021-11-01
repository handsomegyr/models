<?php

namespace App\Common\Models\Tag\Mysql;

use App\Common\Models\Base\Mysql\Base;

class TagToEntity extends Base
{

    /**
     * 标签-标签和实体对应管理
     * This model is mapped to the table itag_to_entity
     */
    public function getSource()
    {
        return 'itag_to_entity';
    }
}
