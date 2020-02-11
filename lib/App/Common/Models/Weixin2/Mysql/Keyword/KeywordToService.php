<?php

namespace App\Common\Models\Weixin2\Mysql\Keyword;

use App\Common\Models\Base\Mysql\Base;

class KeywordToService extends Base
{
    /**
     * 微信-关键词和服务对应
     * This model is mapped to the table iweixin2_keyword_to_service
     */
    public function getSource()
    {
        return 'iweixin2_keyword_to_service';
    }
}
