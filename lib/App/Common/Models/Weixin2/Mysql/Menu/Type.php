<?php

namespace App\Common\Models\Weixin2\Mysql\Menu;

use App\Common\Models\Base\Mysql\Base;

class Type extends Base
{
    /**
     * 微信-自定义菜单类型
     * This model is mapped to the table iweixin2_menu_type
     */
    public function getSource()
    {
        return 'iweixin2_menu_type';
    }
}
