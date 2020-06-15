<?php

namespace App\Common\Models\Database\Mysql;

use App\Common\Models\Base\Mysql\Base;

class OssFile extends Base
{

    /**
     * 数据库-OSS文件管理
     * This model is mapped to the table idatabase_ossfile
     */
    public function getSource()
    {
        return 'idatabase_ossfile';
    }
}
