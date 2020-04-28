<?php

namespace App\Common\Models\Qyweixin\Mysql\Menu;

use App\Common\Models\Base\Mysql\Base;

class Menu extends Base
{
    /**
     * 企业微信-自定义菜单
     * This model is mapped to the table iqyweixin_menu
     */
    public function getSource()
    {
        return 'iqyweixin_menu';
    }
}
