<?php

namespace App\Common\Models\Weixin2\Mysql\TemplateMsg;

use App\Common\Models\Base\Mysql\Base;

class TemplateMsg extends Base
{
    /**
     * 微信-模板消息
     * This model is mapped to the table iweixin2_template_msg
     */
    public function getSource()
    {
        return 'iweixin2_template_msg';
    }
}
