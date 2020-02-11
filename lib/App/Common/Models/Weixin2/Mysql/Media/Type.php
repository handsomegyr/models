<?php

namespace App\Common\Models\Weixin2\Mysql\Media;

use App\Common\Models\Base\Mysql\Base;

class Type extends Base
{
    /**
     * 微信-媒体文件类型
     * This model is mapped to the table iweixin2_media_type
     */
    public function getSource()
    {
        return 'iweixin2_media_type';
    }
}
