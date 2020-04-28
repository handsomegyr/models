<?php

namespace App\Common\Models\Qyweixin\Mysql\Media;

use App\Common\Models\Base\Mysql\Base;

class Type extends Base
{
    /**
     * 企业微信-媒体文件类型
     * This model is mapped to the table iqyweixin_media_type
     */
    public function getSource()
    {
        return 'iqyweixin_media_type';
    }
}
