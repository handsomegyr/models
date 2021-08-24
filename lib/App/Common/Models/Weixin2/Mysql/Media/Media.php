<?php

namespace App\Common\Models\Weixin2\Mysql\Media;

use App\Common\Models\Base\Mysql\Base;

class Media extends Base
{
    /**
     * 微信-临时素材
     * This model is mapped to the table iweixin2_media
     */
    public function getSource()
    {
        return 'iweixin2_media';
    }
    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);

        $data['media_time'] = $this->changeToValidDate($data['media_time']);
        return $data;
    }
}
