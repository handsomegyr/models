<?php

namespace App\Common\Models\Weixin2\Mysql\DataCube;

use App\Common\Models\Base\Mysql\Base;

class ArticleTotal extends Base
{

    protected $table = "iweixin2_datacube_articletotal";

    /**
     * 微信-图文群发总数据
     * This model is mapped to the table iweixin2_datacube_articletotal
     */
    public function getSource()
    {
        return 'iweixin2_datacube_articletotal';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);

        $data['ref_date'] = $this->changeToMongoDate($data['ref_date']);
        $data['stat_date'] = $this->changeToMongoDate($data['stat_date']);
        return $data;
    }
}
