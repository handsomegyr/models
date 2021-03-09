<?php

namespace App\Common\Models\Weixin2\Notification;

use App\Common\Models\Base\Base;

class Task extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Weixin2\Mysql\Notification\Task());
    }

    /**
     * 所有列表
     *
     * @return array
     */
    public function getAll()
    {
        $list = $this->findAll(array(), array('_id' => 1));
        $options = array();
        foreach ($list as $item) {
            $options[$item['_id']] = $item['name'];
        }
        return $options;
    }

    public function checkChangemsginfoCallbackIsValid($changemsginfo_callback)
    {
        // 如果没有设置回调函数的话 那么就直接返回
        if (empty($changemsginfo_callback)) {
            return true;
        } else {
            $changemsginfo_callback_info = \json_decode($changemsginfo_callback, true);
            // 如果不是有效合法的json格式的话就直接返回
            if (empty($changemsginfo_callback_info)) {
                return false;
            } else {
                $className = empty($changemsginfo_callback_info['class']) ? "" : trim($changemsginfo_callback_info['class']);
                $methodName = empty($changemsginfo_callback_info['method']) ? "" : trim($changemsginfo_callback_info['method']);

                if (empty($className)) {
                    return is_callable($methodName);
                } else {
                    $anObject  = new $className();
                    $methodVariable  = array($anObject,  $methodName);
                    return is_callable($methodVariable);
                }
            }
        }
    }
}
