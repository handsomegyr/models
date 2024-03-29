<?php

namespace App\System\Models;

class Activity extends \App\Common\Models\System\Activity
{

    /**
     * 获取所有活动列表
     *
     * @return array
     */
    public function getAll()
    {
        $query = array();
        $sort = array();
        $ret = $this->findAll($query, $sort);
        $list = array();
        foreach ($ret as $item) {
            $list[$item['_id']] = $item['name'];
        }
        return $list;
    }

    private $_activityInfo = null;

    /**
     * 获取活动信息
     *
     * @param string $activity_id            
     */
    public function getActivityInfo($activity_id)
    {
        if ($this->_activityInfo == null) {
            $this->_activityInfo = $this->findOne(array(
                '_id' => $activity_id
            ));
        }
        return $this->_activityInfo;
    }

    /**
     * 检测活动是否开始
     *
     * @param string $activity_id            
     */
    public function checkActivityActive($activity_id)
    {
        $activityInfo = $this->getActivityInfo($activity_id);
        if (!empty($activityInfo['is_actived'])) {
            $now = time();
            if (!empty($activityInfo['start_time']) && !empty($activityInfo['end_time'])) {
                if (strtotime($activityInfo['start_time']) <= $now && $now <= strtotime($activityInfo['end_time'])) {
                    return true;
                } else {
                    return false;
                }
            } else {
                throw new \Exception("请设定完整的活动起止时间");
            }
        } else {
            return false;
        }
    }
}
