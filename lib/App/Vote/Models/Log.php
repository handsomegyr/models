<?php

namespace App\Vote\Models;

class Log extends \App\Common\Models\Vote\Log
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
            '_id' => $sort
        );
        return $sort;
    }

    /**
     * 记录日志
     *
     * @param string $activity            
     * @param string $subject            
     * @param string $item            
     * @param string $identity            
     * @param string $ip            
     * @param string $session_id            
     * @param int $vote_num            
     * @param int $view_num            
     * @param int $share_num            
     * @param array $memo            
     * @return array
     */
    public function log($activity, $subject, $item, $identity, $ip, $session_id, $vote_time, $vote_num, $view_num, $share_num, array $memo = array())
    {
        $data = array();
        $data['activity'] = $activity;
        $data['subject'] = $subject;
        $data['item'] = $item;
        $data['vote_time'] = \App\Common\Utils\Helper::getCurrentTime($vote_time);
        $data['identity'] = $identity;
        $data['ip'] = $ip;
        $data['session_id'] = $session_id;
        $data['vote_num'] = intval($vote_num);
        $data['view_num'] = intval($view_num);
        $data['share_num'] = intval($share_num);
        $data['memo'] = $memo;

        $info = $this->insert($data);
        return $info;
    }

    /**
     * 根据投票人获取他是否投票了$num次
     *
     * @param array $judgeBy            
     * @param int $num            
     * @param array $activitys            
     * @param array $subjects            
     * @param array $items            
     * @param int $startTime            
     * @param int $endTime            
     * @param array $cacheInfo            
     * @throws Exception
     * @return boolean
     */
    public function isVoted(array $judgeBy = array('identity' => NULL, 'ip' => NULL, 'session_id' => NULL), $num = 1, array $activitys = NULL, array $subjects = NULL, array $items = NULL, $startTime = 0, $endTime = 0, array $cacheInfo = array('isCache' => false, 'cacheKey' => null, 'expire_time' => null))
    {
        $query = array();

        if (!empty($judgeBy) && !empty($judgeBy['identity'])) {
            if (is_array($judgeBy['identity'])) {
                $query['identity'] = array(
                    '$in' => $judgeBy['identity']
                );
            } else {
                $query['identity'] = $judgeBy['identity'];
            }
        }
        if (!empty($judgeBy) && !empty($judgeBy['ip'])) {
            if (is_array($judgeBy['ip'])) {
                $query['ip'] = array(
                    '$in' => $judgeBy['ip']
                );
            } else {
                $query['ip'] = $judgeBy['ip'];
            }
        }
        if (!empty($judgeBy) && !empty($judgeBy['session_id'])) {
            if (is_array($judgeBy['session_id'])) {
                $query['session_id'] = array(
                    '$in' => $judgeBy['session_id']
                );
            } else {
                $query['session_id'] = $judgeBy['session_id'];
            }
        }
        if (!empty($activitys)) {
            $query['activity'] = array(
                '$in' => $activitys
            );
        }
        if (!empty($subjects)) {
            $query['subject'] = array(
                '$in' => $subjects
            );
        }
        if (!empty($items)) {
            $query['item'] = array(
                '$in' => $items
            );
        }

        if (!empty($startTime)) {
            $query['vote_time']['$gte'] = \App\Common\Utils\Helper::getCurrentTime($startTime);
        }
        if (!empty($endTime)) {
            $query['vote_time']['$lte'] = \App\Common\Utils\Helper::getCurrentTime($endTime);
        }

        if (intval($num) < 1) {
            throw new \Exception("参数num的值不正确");
        }

        $isVoted = false;
        if (!empty($cacheInfo) && !empty($cacheInfo['isCache']) && !empty($cacheInfo['cacheKey'])) {
            $cache = $this->getDI()->get("cache");
            $cacheKey = md5($cacheInfo['cacheKey'] . 'num' . $num . "_condition_" . md5(serialize($query)));
            $isVoted = $cache->get($cacheKey);
        }
        if (empty($isVoted)) {
            $count = $this->count($query);
            $isVoted = ($count > ($num - 1));
            if ($isVoted) {
                if (!empty($cacheInfo) && !empty($cacheInfo['isCache']) && !empty($cacheInfo['cacheKey'])) {
                    $cache->save($cacheKey, $isVoted, empty($cacheInfo['expire_time']) ? null : $cacheInfo['expire_time']);
                }
            }
        }
        return $isVoted;
    }
}
