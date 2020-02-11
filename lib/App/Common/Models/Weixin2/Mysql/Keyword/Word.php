<?php

namespace App\Common\Models\Weixin2\Mysql\Keyword;

use App\Common\Models\Base\Mysql\Base;

class Word extends Base
{
    /**
     * 微信-非关键词
     * This model is mapped to the table iweixin2_word
     */
    public function getSource()
    {
        return 'iweixin2_word';
    }
}
