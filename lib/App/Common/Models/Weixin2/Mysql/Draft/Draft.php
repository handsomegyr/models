<?php

namespace App\Common\Models\Weixin2\Mysql\Draft;

use App\Common\Models\Base\Mysql\Base;

class Draft extends Base
{
    /**
     * 微信-草稿箱
     * This model is mapped to the table iweixin2_draft
     */
    public function getSource()
    {
        return 'iweixin2_draft';
    }
    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['media_time'] = $this->changeToValidDate($data['media_time']);
        $data['publish_time'] = $this->changeToValidDate($data['publish_time']);
        return $data;
    }
}
