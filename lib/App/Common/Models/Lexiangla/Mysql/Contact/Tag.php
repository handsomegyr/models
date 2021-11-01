<?php

namespace App\Common\Models\Lexiangla\Mysql\Contact;

use App\Common\Models\Base\Mysql\Base;

class Tag extends Base
{

    /**
     * 乐享-通讯录管理-标签管理
     * This model is mapped to the table ilexiangla_tag
     */
    public function getSource()
    {
        return 'ilexiangla_tag';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['sync_time'] = $this->changeToValidDate($data['sync_time']);
        $data['is_exist'] = $this->changeToBoolean($data['is_exist']);
        return $data;
    }
}
