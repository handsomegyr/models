<?php

namespace App\Weixincard\Models;

class Event extends \App\Common\Models\Weixincard\Event
{

    /**
     * 记录
     *
     * @param string $ToUserName            
     * @param string $FromUserName            
     * @param int $CreateTime            
     * @param string $MsgType            
     * @param string $Event            
     * @param string $CardId            
     * @param string $FriendUserName            
     * @param int $IsGiveByFriend            
     * @param string $UserCardCode            
     * @param string $OuterId            
     * @param string $xml_data            
     */
    public function record($ToUserName, $FromUserName, $CreateTime, $MsgType, $Event, $CardId, $FriendUserName, $IsGiveByFriend, $UserCardCode, $OuterId, $xml_data)
    {
        $data = array();
        $data['ToUserName'] = $ToUserName;
        $data['FromUserName'] = $FromUserName;
        $data['CreateTime'] = \App\Common\Utils\Helper::getCurrentTime(intval($CreateTime));
        $data['MsgType'] = $MsgType;
        $data['Event'] = $Event;
        $data['CardId'] = (string) $CardId;
        $data['FriendUserName'] = $FriendUserName;
        $data['IsGiveByFriend'] = intval($IsGiveByFriend);
        $data['UserCardCode'] = strval($UserCardCode);
        $data['OuterId'] = intval($OuterId);
        $data['xml_data'] = strval($xml_data);
        $this->insert($data);
    }
}
