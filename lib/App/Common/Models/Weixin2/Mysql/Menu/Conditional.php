<?php

namespace App\Common\Models\Weixin2\Mysql\Menu;

use App\Common\Models\Base\Mysql\Base;

class Conditional extends Base
{
    /**
     * 微信-个性化菜单
     * This model is mapped to the table iweixin2_menu_conditional
     */
    public function getSource()
    {
        return 'iweixin2_menu_conditional';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['is_show'] = $this->changeToBoolean($data['is_show']);
        $data['menu_time'] = $this->changeToValidDate($data['menu_time']);
        return $data;
    }
}
