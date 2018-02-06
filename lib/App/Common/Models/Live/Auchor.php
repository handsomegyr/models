<?php
namespace App\Common\Models\Live;

use App\Common\Models\Base\Base;

class Auchor extends Base
{
    use \App\Common\Models\Live\Redis;

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Live\Mysql\Auchor());
        $this->redis = $this->getDI()->get('redis');
    }

    public function getImagePath($baseUrl, $image, $x = 0, $y = 0)
    {
        $uploadPath = $this->getUploadPath();
        // return "{$baseUrl}upload/{$uploadPath}/{$image}";
        $xyStr = "";
        if (! empty($x)) {
            $xyStr .= "&w={$x}";
        }
        if (! empty($y)) {
            $xyStr .= "&h={$y}";
        }
        return "{$baseUrl}service/file/index?id={$image}&upload_path={$uploadPath}{$xyStr}";
    }

    public function getUploadPath()
    {
        return trim("auchor", '/');
    }
}