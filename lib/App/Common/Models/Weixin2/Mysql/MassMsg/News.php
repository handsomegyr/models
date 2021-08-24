<?php

namespace App\Common\Models\Weixin2\Mysql\MassMsg;

use App\Common\Models\Base\Mysql\Base;

class News extends Base
{

    /**
     * 微信-群发消息图文
     * This model is mapped to the table iweixin2_mass_msg_news
     */
    public function getSource()
    {
        return 'iweixin2_mass_msg_news';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);

        $data['media_time'] = $this->changeToValidDate($data['media_time']);

        $data['show_cover_pic'] = $this->changeToBoolean($data['show_cover_pic']);
        $data['need_open_comment'] = $this->changeToBoolean($data['need_open_comment']);
        // $data['only_fans_can_comment'] = $this->changeToBoolean($data['only_fans_can_comment']);

        return $data;
    }
}
