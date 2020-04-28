<?php

namespace App\Common\Models\Qyweixin\Mysql\Keyword;

use App\Common\Models\Base\Mysql\Base;

class Word extends Base
{
    /**
     * 企业微信-非关键词
     * This model is mapped to the table iqyweixin_word
     */
    public function getSource()
    {
        return 'iqyweixin_word';
    }
}
