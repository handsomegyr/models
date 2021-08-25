<?php

namespace App\Game\Models;

class Game extends \App\Common\Models\Game\Game
{

    public function getGameInfo($game_id, $now, $is_return_info = true)
    {
        $ret = array();
        // 获取游戏信息
        $gameInfo = $this->getInfoById($game_id);
        if (empty($gameInfo)) {
            $is_game_started = false;
            $is_game_over = false;
            $gameInfo = array();
        } else {
            $is_game_started = $this->isGameStarted($gameInfo, $now);
            $is_game_over = $this->isGameOver($gameInfo, $now);
        }
        // 游戏是否开始了
        $ret['is_game_started'] = $is_game_started;
        // 游戏是否结束了
        $ret['is_game_over'] = $is_game_over;

        if (!empty($is_return_info)) {
            // 游戏信息
            $ret = array_merge($gameInfo, $ret);
        }
        return $ret;
    }

    /**
     * 检查游戏是否开始了
     *
     * @param array $gameInfo            
     */
    protected function isGameStarted($gameInfo, $now)
    {
        if (empty($gameInfo)) {
            return false;
        } else {
            if (strtotime($gameInfo['start_time']) <= $now) {
                return true;
            } else {
                return false;
            }
        }
    }

    /**
     * 检查游戏是否结束
     *
     * @param array $gameInfo            
     */
    protected function isGameOver($gameInfo, $now)
    {
        if (empty($gameInfo)) {
            return false;
        } else {
            if (strtotime($gameInfo['end_time']) < $now) {
                return true;
            } else {
                return false;
            }
        }
    }

    /**
     * 更新游戏的统计信息
     *
     * @param string $id            
     * @param number $score            
     * @param number $user_num            
     * @param int $now            
     */
    public function updateStatistics($id, $score, $user_num, $now)
    {
        $max_score_time = date("Y-m-d H:i:s", $now);

        $incData  = array();
        if (!empty($score)) {
            $incData['total_score'] = $score;
        }
        if (!empty($user_num)) {
            $incData['total_user_num'] = $user_num;
        }
        $incData['total_play_num'] = 1;

        $affectRows = $this->update(array('_id' => $id), array(
            //'$set' => $updateData,
            '$inc' => $incData,
            '$exp' => array(
                'max_score_time' => " CASE WHEN (max_score<{$score}) THEN '{$max_score_time}' ELSE max_score_time END ",
                'max_score' => " CASE WHEN (max_score<{$score}) THEN {$score} ELSE max_score END "
            )
        ));

        return $affectRows;
    }
}
