<?php

namespace App\Common\Models\Database\Mysql;

use App\Common\Models\Base\Mysql\Base;

class File extends Base
{

    /**
     * 数据库-文件管理
     * This model is mapped to the table idatabase_file
     */
    public function getSource()
    {
        return 'idatabase_file';
    }
}
