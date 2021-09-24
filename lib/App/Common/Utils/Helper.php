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

    /**
     * \App\Common\Utils\Helper::myJsonEncode(不转义斜杠和中文)
     *
     * @param mixed $data
     * @return false|string
     */
    public static function myJsonEncode($data)
    {
        $options = JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRESERVE_ZERO_FRACTION;

        return json_encode($data, $options);
    }
}
