<?php

namespace App\Common\Models\Weixin2\Mysql\Miniprogram;

use App\Common\Models\Base\Mysql\Base;

class Scene extends Base
{
    /**
     * 微信-小程序场景值
     * This model is mapped to the table iweixin2_miniprogram_scene
     */
    public function getSource()
    {
        return 'iweixin2_miniprogram_scene';
    }
}
