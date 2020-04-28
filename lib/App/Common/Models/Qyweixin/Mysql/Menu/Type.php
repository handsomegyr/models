<?php

namespace App\Common\Models\Qyweixin\Mysql\Menu;

use App\Common\Models\Base\Mysql\Base;

class Type extends Base
{
    /**
     * 企业微信-自定义菜单类型
     * This model is mapped to the table iqyweixin_menu_type
     */
    public function getSource()
    {
        return 'iqyweixin_menu_type';
    }
}
