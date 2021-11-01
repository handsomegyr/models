<?php

namespace App\Common\Models\Lexiangla\Mysql\Contact;

use App\Common\Models\Base\Mysql\Base;

class TagSync extends Base
{

    /**
     * 乐享-通讯录管理-标签同步管理
     * This model is mapped to the table ilexiangla_tag_sync
     */
    public function getSource()
    {
        return 'ilexiangla_tag_sync';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['sync_time'] = $this->changeToValidDate($data['sync_time']);
        return $data;
    }
}
