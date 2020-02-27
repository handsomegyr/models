<?php

namespace App\Common\Models\Search\Mysql;

use App\Common\Models\Base\Mysql\Base;

class Keyword extends Base
{

    /**
     * 搜索-关键词管理
     * This model is mapped to the table isearch_keyword
     */
    public function getSource()
    {
        return 'isearch_keyword';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['search_time'] = $this->changeToMongoDate($data['search_time']);
        return $data;
    }
}
