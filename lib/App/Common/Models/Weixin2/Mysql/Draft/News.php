<?php

namespace App\Common\Models\Weixin2\Mysql\Draft;

use App\Common\Models\Base\Mysql\Base;

class News extends Base
{

    /**
     * 微信-草稿箱图文
     * This model is mapped to the table iweixin2_draft_news
     */
    public function getSource()
    {
        return 'iweixin2_draft_news';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['show_cover_pic'] = $this->changeToBoolean($data['show_cover_pic']);
        $data['need_open_comment'] = $this->changeToBoolean($data['need_open_comment']);
        // $data['only_fans_can_comment'] = $this->changeToBoolean($data['only_fans_can_comment']);
        $data['update_time'] = $this->changeToValidDate($data['update_time']);
        $data['create_time'] = $this->changeToValidDate($data['create_time']);
        $data['is_exist'] = $this->changeToBoolean($data['is_exist']);
        $data['sync_time'] = $this->changeToValidDate($data['sync_time']);
        return $data;
    }
}
