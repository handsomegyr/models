<?php

namespace App\Common\Models\Weixin2\Mysql\FreePublish;

use App\Common\Models\Base\Mysql\Base;

class Article extends Base
{

    /**
     * 微信-已发布文章
     * This model is mapped to the table weixinopen_freepublish_article
     */
    public function getSource()
    {
        return 'weixinopen_freepublish_article';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['show_cover_pic'] = $this->changeToBoolean($data['show_cover_pic']);
        $data['need_open_comment'] = $this->changeToBoolean($data['need_open_comment']);
        // $data['only_fans_can_comment'] = $this->changeToBoolean($data['only_fans_can_comment']);
        $data['is_deleted'] = $this->changeToBoolean($data['is_deleted']);
        $data['update_time'] = $this->changeToValidDate($data['update_time']);
        $data['create_time'] = $this->changeToValidDate($data['create_time']);
        $data['is_exist'] = $this->changeToBoolean($data['is_exist']);
        $data['sync_time'] = $this->changeToValidDate($data['sync_time']);
        return $data;
    }
}
