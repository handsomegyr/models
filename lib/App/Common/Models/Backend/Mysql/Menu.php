<?php

namespace App\Common\Models\Backend\Mysql;

use App\Common\Models\Base\Mysql\Base;

class Menu extends Base
{

    /**
     * This model is mapped to the table ibackend_menu
     */
    public function getSource()
    {
        return 'ibackend_menu';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['is_show'] = $this->changeToBoolean($data['is_show']);
        return $data;
    }
}
