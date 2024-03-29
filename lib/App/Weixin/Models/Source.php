<?php
namespace App\Weixin\Models;

class Source extends \App\Common\Models\Weixin\Source
{

    /**
     * 获取信息接收信息
     *
     * @return array
     */
    public function revieve($postStr)
    {
        $datas = (array) simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
        $datas = $this->object2array($datas);
        
        if (isset($datas['Event']) && $datas['Event'] === 'LOCATION') {
            $Latitude = isset($datas['Latitude']) ? floatval($datas['Latitude']) : 0;
            $Longitude = isset($datas['Longitude']) ? floatval($datas['Longitude']) : 0;
            $datas['coordinate'] = array(
                $Latitude,
                $Longitude
            );
        }
        
        if (isset($datas['MsgType']) && $datas['MsgType'] === 'location') {
            $Location_X = isset($datas['Location_X']) ? floatval($datas['Location_X']) : 0;
            $Location_Y = isset($datas['Location_Y']) ? floatval($datas['Location_Y']) : 0;
            $datas['coordinate'] = array(
                $Location_X,
                $Location_Y
            );
        }
        return $datas;
    }

    /**
     * 转化方法 很重要
     *
     * @param object $object            
     */
    public function object2array($object)
    {
        return @json_decode(preg_replace('/{}/', '""', @\App\Common\Utils\Helper::myJsonEncode($object)), 1);
    }
}