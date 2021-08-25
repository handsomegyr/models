<?php

namespace App\Common\Utils;

class Helper
{
    public static function getCurrentTime($time = 0)
    {
        if (empty($time)) {
            if (defined('CURRENT_TIMESTAMP')) {
                return date('Y-m-d H:i:s', CURRENT_TIMESTAMP);
            } else {
                return date('Y-m-d H:i:s');
            }
        } else {
            return date('Y-m-d H:i:s', $time);
        }
    }
}
