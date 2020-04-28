<?php

namespace App\Common\Models\Qyweixin\Mysql\Keyword;

use App\Common\Models\Base\Mysql\Base;

class KeywordToService extends Base
{
    /**
     * 企业微信-关键词和服务对应
     * This model is mapped to the table iqyweixin_keyword_to_service
     */
    public function getSource()
    {
        return 'iqyweixin_keyword_to_service';
    }
}
