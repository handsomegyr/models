<?php
namespace App\Common\Models\Weixinredpack\Mysql;

use App\Common\Models\Base\Mysql\Base;

class Redpack extends Base
{

    /**
     * 微信红包-红包信息
     * This model is mapped to the table iweixinredpack_redpack
     */
    public function getSource()
    {
        return 'iweixinredpack_redpack';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['start_time'] = $this->changeToValidDate($data['start_time']);
        $data['end_time'] = $this->changeToValidDate($data['end_time']);
        return $data;
    }
}
