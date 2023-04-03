<?php

namespace App\Common\Models\Weixin2\Mysql\FreePublish;

use App\Common\Models\Base\Mysql\Base;

class FreePublish extends Base
{
    /**
     * 微信-发布
     * This model is mapped to the table weixinopen_freepublish
     */
    public function getSource()
    {
        return 'weixinopen_freepublish';
    }
    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['article_detail'] = $this->changeToArray($data['article_detail']);
        $data['fail_idx'] = $this->changeToArray($data['fail_idx']);
        $data['get_time'] = $this->changeToValidDate($data['get_time']);
        return $data;
    }
}
