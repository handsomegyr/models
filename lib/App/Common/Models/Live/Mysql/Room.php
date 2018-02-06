<?php
namespace App\Common\Models\Live\Mysql;

use App\Common\Models\Base\Mysql\Base;

class Room extends Base
{

    /**
     * 直播-房间管理
     * This model is mapped to the table ilive_room
     */
    public function getSource()
    {
        return 'ilive_room';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['is_opened'] = $this->changeToBoolean($data['is_opened']);
        $data['start_time'] = $this->changeToMongoDate($data['start_time']);
        $data['end_time'] = $this->changeToMongoDate($data['end_time']);
        $data['is_test'] = $this->changeToBoolean($data['is_test']);
        $data['is_direct'] = $this->changeToBoolean($data['is_direct']);
        $data['live_start_time'] = $this->changeToMongoDate($data['live_start_time']);
        $data['live_end_time'] = $this->changeToMongoDate($data['live_end_time']);
        $data['live_is_closed'] = $this->changeToBoolean($data['live_is_closed']);
        $data['live_is_paused'] = $this->changeToBoolean($data['live_is_paused']);
        $data['live_is_replay'] = $this->changeToBoolean($data['live_is_replay']);
        $data['share_settings'] = $this->changeToArray($data['share_settings']);
        $data['robot_settings'] = $this->changeToArray($data['robot_settings']);
        $data['item_settings'] = $this->changeToArray($data['item_settings']);
        $data['behavior_settings'] = $this->changeToArray($data['behavior_settings']);
        $data['plugin_settings'] = $this->changeToArray($data['plugin_settings']);
        $data['view_settings'] = $this->changeToArray($data['view_settings']);
        $data['task_settings'] = $this->changeToArray($data['task_settings']);
        $data['emoji_settings'] = $this->changeToArray($data['emoji_settings']);
        $data['category_settings'] = $this->changeToArray($data['category_settings']);
        $data['coupon_settings'] = $this->changeToArray($data['coupon_settings']);
        $data['banner_settings'] = $this->changeToArray($data['banner_settings']);
        $data['tag_settings'] = $this->changeToArray($data['tag_settings']);
        $data['memo'] = $this->changeToArray($data['memo']);
        return $data;
    }
}