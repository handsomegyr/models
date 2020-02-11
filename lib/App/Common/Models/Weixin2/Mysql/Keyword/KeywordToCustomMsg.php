<?php

namespace App\Common\Models\Weixin2\Mysql\Keyword;

use App\Common\Models\Base\Mysql\Base;

class KeywordToCustomMsg extends Base
{

    /**
     * 微信-关键词和客服消息对应
     * This model is mapped to the table iweixin2_keyword_to_custommsg
     */
    public function getSource()
    {
        return 'iweixin2_keyword_to_custommsg';
    }
}
