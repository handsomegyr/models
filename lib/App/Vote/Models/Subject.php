<?php

namespace App\Vote\Models;

class Subject extends \App\Common\Models\Vote\Subject
{

    /**
     * 默认排序
     *
     * @param int $sort            
     * @return array
     */
    public function getDefaultSort($sort = -1)
    {
        $sort = array(
            'show_order' => -1,
            '_id' => $sort
        );
        return $sort;
    }

    /**
     * 根据投票数排序
     *
     * @param int $sort            
     * @return array
     */
    public function getRankSort($sort = -1)
    {
        $sort = array(
            'vote_count' => $sort
        );
        return $sort;
    }


    /**
     * 根据活动ID获取场主题列表
     *
     * @param string $activityId 
     * @param int $now           
     * @return array
     */
    public function getListByActivityId($activityId, $now)
    {
        $now = \App\Common\Utils\Helper::getCurrentTime($now);
        $query = array(
            "is_closed" => false,
            'start_time' => array(
                '$lte' => $now
            ),
            'end_time' => array(
                '$gte' => $now
            )
        ); // 显示
        $query['activity_id'] = $activityId;
        $sort = $this->getDefaultSort();
        $ret = $this->findAll($query, $sort);
        return $ret;
    }

    /**
     * 增加投票数
     *
     * @param string $id            
     * @param int $vote_count            
     */
    public function incVoteCount($id, $vote_count = 1, $view_count = 0, $share_count = 0)
    {
        $query = array(
            '_id' => ($id)
        );
        $this->update($query, array(
            '$inc' => array(
                'vote_count' => $vote_count,
                'view_count' => $view_count,
                'share_count' => $share_count
            )
        ));
    }

    /**
     * 我的排名
     *
     * @param array $myInfo    
     * @param int $now          
     * @param array $otherConditions            
     * @return number
     */
    public function getRank($myInfo, $now, array $otherConditions = array())
    {
        $now = \App\Common\Utils\Helper::getCurrentTime($now);
        $query = array(
            "is_closed" => false,
            'start_time' => array(
                '$lte' => $now
            ),
            'end_time' => array(
                '$gte' => $now
            )
        ); // 显示
        $query['_id'] = array(
            '$ne' => $myInfo['_id']
        );
        $query['vote_count'] = array(
            '$gt' => $myInfo['vote_count']
        ); // 按投票次数
        if (!empty($otherConditions)) {
            foreach ($otherConditions as $key => $value) {
                $query[$key] = $value;
            }
        }
        $num = $this->count($query);
        return $num + 1;
    }
}
