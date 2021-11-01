<?php

namespace App\Common\Models\Lexiangla\Mysql\Contact;

use App\Common\Models\Base\Mysql\Base;

class TagUser extends Base
{

    /**
     * 乐享-通讯录管理-成员标签管理
     * This model is mapped to the table ilexiangla_tag_user
     */
    public function getSource()
    {
        return 'ilexiangla_tag_user';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['get_time'] = $this->changeToValidDate($data['get_time']);
        $data['is_exist'] = $this->changeToBoolean($data['is_exist']);
        return $data;
    }
}
