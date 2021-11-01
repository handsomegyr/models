<?php

namespace App\Common\Models\Lexiangla\Mysql\Contact;

use App\Common\Models\Base\Mysql\Base;

class UserSync extends Base
{

    /**
     * 乐享-通讯录管理-成员同步管理
     * This model is mapped to the table ilexiangla_user_sync
     */
    public function getSource()
    {
        return 'ilexiangla_user_sync';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['sync_time'] = $this->changeToValidDate($data['sync_time']);
        return $data;
    }
}
