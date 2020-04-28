<?php

namespace App\Common\Models\Qyweixin\Mysql\Media;

use App\Common\Models\Base\Mysql\Base;

class Media extends Base
{
    /**
     * 企业微信-临时素材
     * This model is mapped to the table iqyweixin_media
     */
    public function getSource()
    {
        return 'iqyweixin_media';
    }
    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);

        $data['media_time'] = $this->changeToMongoDate($data['media_time']);
        return $data;
    }
}
