<?php

namespace App\Common\Models\Weixin2\Notification;

use App\Common\Models\Base\Base;

class TaskLog extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Weixin2\Mysql\Notification\TaskLog());
    }

    public function changeMsgInfo($taskLog, $msgInfo)
    {
        // 如果没有设置回调函数的话 那么就直接返回
        if (empty($taskLog['changemsginfo_callback'])) {
            return $msgInfo;
        } else {
            $changemsginfo_callback_info = \json_decode($taskLog['changemsginfo_callback'], true);
            // 如果不是有效合法的json格式的话就直接返回
            if (empty($changemsginfo_callback_info)) {
                return $msgInfo;
            } else {
                $className = empty($changemsginfo_callback_info['class']) ? "" : trim($changemsginfo_callback_info['class']);
                $methodName = empty($changemsginfo_callback_info['method']) ? "" : trim($changemsginfo_callback_info['method']);
                $userid = $taskLog['userid'];
                if (empty($className)) {
                    return call_user_func($methodName, $userid, $msgInfo);
                } else {
                    return call_user_func(array($className, $methodName), $userid, $msgInfo);
                }
            }
        }
    }
}
