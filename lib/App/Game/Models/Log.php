<?php

namespace App\Game\Models;

class Log extends \App\Common\Models\Game\Log
{

    /**
     * 记录日志
     *
     * @param string $activity_id            
     * @param string $game_id            
     * @param string $user_id            
     * @param string $user_nickname            
     * @param string $user_headimgurl            
     * @param number $game_score            
     * @param number $game_score2            
     * @param string $game_file            
     * @param string $game_img            
     * @param string $ip            
     * @param number $play_time            
     * @param array $memo            
     * @return array
     */
    public function log($activity_id, $game_id, $user_id, $user_nickname, $user_headimgurl, $game_score, $game_score2, $game_file, $game_img, $ip, $play_time, $memo = array('memo' => ''))
    {
        $data = array(
            'activity_id' => $activity_id,
            'game_id' => $game_id,
            'user_id' => $user_id,
            'user_nickname' => $user_nickname,
            'user_headimgurl' => $user_headimgurl,
            'game_score' => $game_score,
            'game_score2' => $game_score2,
            'game_file' => $game_file,
            'game_img' => $game_img,
            'play_time' => getCurrentTime($play_time),
            'ip' => $ip,
            'memo' => $memo
        );
        $res = $this->insert($data);
        return $res;
    }
}
