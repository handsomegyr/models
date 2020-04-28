<?php

namespace App\Common\Models\Qyweixin\Mysql\Keyword;

use App\Common\Models\Base\Mysql\Base;

class Keyword extends Base
{
    /**
     * 企业微信-关键词
     * This model is mapped to the table iqyweixin_keyword
     */
    public function getSource()
    {
        return 'iqyweixin_keyword';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['is_fuzzy'] = $this->changeToBoolean($data['is_fuzzy']);
        return $data;
    }
}
