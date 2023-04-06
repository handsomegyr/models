<?php

namespace App\Common\Models\Weixin2\Mysql\Menu;

use App\Common\Models\Base\Mysql\Base;

class Menu extends Base
{
    /**
     * 微信-自定义菜单
     * This model is mapped to the table iweixin2_menu
     */
    public function getSource()
    {
        return 'iweixin2_menu';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['is_show'] = $this->changeToBoolean($data['is_show']);
        return $data;
    }
}
