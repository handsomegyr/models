<?php

namespace App\Common\Models\Prize\Mysql;

use App\Common\Models\Base\Mysql\Base;

class Prize extends Base
{

    /**
     * 奖品-奖品
     * This model is mapped to the table iprize_prize
     */
    public function getSource()
    {
        return 'iprize_prize';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);

        $data['is_virtual'] = $this->changeToBoolean($data['is_virtual']);
        $data['is_need_virtual_code'] = $this->changeToBoolean($data['is_need_virtual_code']);
        $data['is_valid'] = $this->changeToBoolean($data['is_valid']);

        $data['ad_imgs'] = $this->changeToArray($data['ad_imgs']);
        $data['cover_imgs'] = $this->changeToArray($data['cover_imgs']);
        $data['desc_imgs'] = $this->changeToArray($data['desc_imgs']);
        $data['carousel_imgs'] = $this->changeToArray($data['carousel_imgs']);
        $data['share_imgs'] = $this->changeToArray($data['share_imgs']);
        $data['video_cover_imgs'] = $this->changeToArray($data['video_cover_imgs']);

        return $data;
    }
}
