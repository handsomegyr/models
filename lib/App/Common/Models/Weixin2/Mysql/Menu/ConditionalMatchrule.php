<?php

namespace App\Common\Models\Weixin2\Mysql\Menu;

use App\Common\Models\Base\Mysql\Base;

class ConditionalMatchrule extends Base
{
    /**
     * 微信-个性化菜单匹配规则
     * This model is mapped to the table iweixin2_menu_conditional_matchrule
     */
    public function getSource()
    {
        return 'iweixin2_menu_conditional_matchrule';
    }
}
