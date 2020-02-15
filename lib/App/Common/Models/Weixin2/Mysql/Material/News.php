<?php

namespace App\Common\Models\Weixin2\Mysql\Material;

use App\Common\Models\Base\Mysql\Base;

class News extends Base
{
    /**
     * 微信-永久图文素材
     * This model is mapped to the table iweixin2_material_news
     */
    public function getSource()
    {
        return 'iweixin2_material_news';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);

        $data['media_time'] = $this->changeToMongoDate($data['media_time']);

        $data['show_cover_pic'] = $this->changeToBoolean($data['show_cover_pic']);
        $data['need_open_comment'] = $this->changeToBoolean($data['need_open_comment']);
        // $data['only_fans_can_comment'] = $this->changeToBoolean($data['only_fans_can_comment']);
        return $data;
    }
}
