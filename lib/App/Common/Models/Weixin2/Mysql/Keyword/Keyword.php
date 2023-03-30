<?php

namespace App\Common\Models\Weixin2\Mysql\Keyword;

use App\Common\Models\Base\Mysql\Base;

class Keyword extends Base
{
    /**
     * 微信-关键词
     * This model is mapped to the table iweixin2_keyword
     */
    public function getSource()
    {
        return 'iweixin2_keyword';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['is_fuzzy'] = $this->changeToBoolean($data['is_fuzzy']);
        $data['start_time'] = $this->changeToValidDate($data['start_time']);
        $data['end_time'] = $this->changeToValidDate($data['end_time']);
        $data['is_actived'] = $this->changeToBoolean($data['is_actived']);
        return $data;
    }
}
