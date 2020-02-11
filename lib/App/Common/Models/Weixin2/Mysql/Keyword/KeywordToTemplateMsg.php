<?php

namespace App\Common\Models\Weixin2\Mysql\Keyword;

use App\Common\Models\Base\Mysql\Base;

class KeywordToTemplateMsg extends Base
{
    /**
     * 微信-关键词和模板消息对应
     * This model is mapped to the table iweixin2_keyword_to_templatemsg
     */
    public function getSource()
    {
        return 'iweixin2_keyword_to_templatemsg';
    }
}
