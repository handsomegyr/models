<?php

namespace App\Game\Models;

class User extends \App\Common\Models\Game\User
{

    /**
     * 根据userid生成或获取记录
     */
    public function getInfoByUserId($user_id, $game_id, $activity_id)
    {
        return $this->findOne(array(
            'user_id' => trim($user_id),
            'game_id' => $game_id,
            'activity_id' => $activity_id
        ));
    }

    /**
     * 生成记录
     */
    public function create($activity_id, $game_id, $user_id, $user_nickname, $user_headimgurl, $max_score, $max_score_time, array $memo = array('memo' => ''))
    {
        $data = array();
        $data['activity_id'] = $activity_id; // 活动
        $data['game_id'] = $game_id; // 游戏
        $data['user_id'] = $user_id; // 微信ID
        $data['user_nickname'] = trim($user_nickname); // 昵称
        $data['user_headimgurl'] = trim($user_headimgurl); // 头像
        $data['max_score'] = intval($max_score); // 最大游戏分数
        $data['max_score_time'] = getCurrentTime($max_score_time); // 最大游戏分数游戏时间
        $data['total_score'] = intval($max_score); // 游戏总分数
        $data['total_num'] = 1; // 游戏次数
        $data['memo'] = $memo; // 备注
        return $this->insert($data);
    }

    /**
     * 记录单人游戏分数，更新游戏的最高分
     *
     * @param array $gameUserInfo            
     * @param number $score            
     * @param number $now            
     */
    public function updateScore($gameUserInfo, $score, $now)
    {
        $id = $gameUserInfo['_id'];
        $max_score_time = date("Y-m-d H:i:s", $now);

        $incData  = array();
        if (!empty($score)) {
            $incData['total_score'] = $score;
        }
        $incData['total_num'] = 1;

        $affectRows = $this->update(array('_id' => $id), array(
            // '$set' => $updateData,
            '$inc' => $incData,
            '$exp' => array(
                'max_score_time' => " CASE WHEN (max_score<{$score}) THEN '{$max_score_time}' ELSE max_score_time END ",
                'max_score' => " CASE WHEN (max_score<{$score}) THEN {$score} ELSE max_score END "
            )
        ));


        return $affectRows;
    }

    /**
     * 按游戏分数和游戏时间，获取排名列表
     *
     * @param array $gameInfo            
     * @param number $page            
     * @param number $limit            
     * @return array
     */
    public function getRankList($gameInfo, $page, $limit)
    {
        $query = array(
            'game_id' => $gameInfo['_id'],
            'activity_id' => $gameInfo['activity_id']
        );
        $sort = array();
        $sort['max_score'] = -1;
        $sort['max_score_time'] = 1;
        $ret = $this->find($query, $sort, ($page - 1) * $limit, $limit);

        return $ret;
    }

    /**
     * 按游戏分数和游戏时间，获取我的排名
     *
     * @param array $gameUserInfo            
     * @param array $gameInfo            
     * @return array
     */
    public function getMyRank($gameUserInfo, $gameInfo)
    {
        $myScore = intval($gameUserInfo["max_score"]);
        $myScoreTime = date('Y-m-d H:i:s', $gameUserInfo["max_score_time"]->sec);

        $query = array(
            'user_id' => array('$ne' => $gameUserInfo['user_id']),
            'game_id' => $gameUserInfo['game_id'],
            'activity_id' => $gameUserInfo['activity_id'],
            '$exp' => " ((max_score > {$myScore}) or (max_score = {$myScore} and max_score_time < '{$myScoreTime}')) "
        );
        $num = $this->count($query);
        return array(
            // 我的排名
            "myRank" => ($num + 1),
            // 战胜了百分比
            "winPercent" => ($gameInfo["total_user_num"] - $num) * 100.00 / $gameInfo["total_user_num"],
            // 我的积分
            "myScore" => $myScore
        );
    }
}
