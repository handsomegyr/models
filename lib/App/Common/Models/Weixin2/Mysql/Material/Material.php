<?php

namespace App\Common\Models\Weixin2\Mysql\Material;

use App\Common\Models\Base\Mysql\Base;

class Material extends Base
{
    /**
     * 微信-永久素材
     * This model is mapped to the table iweixin2_material
     */
    public function getSource()
    {
        return 'iweixin2_material';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);

        $data['media_time'] = $this->changeToValidDate($data['media_time']);
        $data['delete_media_time'] = $this->changeToValidDate($data['delete_media_time']);
        return $data;
    }
}
