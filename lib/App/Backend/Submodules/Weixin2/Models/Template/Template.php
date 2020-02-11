<?php

namespace App\Backend\Submodules\Weixin2\Models\Template;

class Template extends \App\Common\Models\Weixin2\Template\Template
{

    use \App\Backend\Models\Base;

    /**
     * 获取所有列表
     *
     * @return array
     */
    public static function getAll()
    {
        $list = self::orderBy('id', 'asc')
            ->select('title', 'template_id')
            ->get();
        $options = array();
        foreach ($list as $item) {
            $options[$item->template_id] = "{$item->title}";
        }
        return $options;
    }
}
