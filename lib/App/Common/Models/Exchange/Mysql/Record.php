<?php

namespace App\Common\Models\Exchange\Mysql;

use App\Common\Models\Base\Mysql\Base;

class Record extends Base
{

    /**
     * 兑换日志记录
     * This model is mapped to the table iexchange_record
     */
    public function getSource()
    {
        return 'iexchange_record';
    }
}
