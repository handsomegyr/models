<?php

namespace App\Weixin2\Models\Msg;

class Log extends \App\Common\Models\Weixin2\Msg\Log
{

    /**
     * 记录日志
     *
     * @param array $info            
     * @return array()
     */
    public function record($info)
    {
        $datas = $this->getPrepareData($info);
        return $this->insert($datas);
    }

    private function getPrepareData($info)
    {
        $data = array();

        $data['component_appid'] = isset($info['component_appid']) ? $info['component_appid'] : '';
        $data['authorizer_appid'] = isset($info['authorizer_appid']) ? $info['authorizer_appid'] : '';
        $data['ToUserName'] = isset($info['ToUserName']) ? $info['ToUserName'] : '';
        $data['FromUserName'] = isset($info['FromUserName']) ? $info['FromUserName'] : '';
        $data['CreateTime'] = isset($info['CreateTime']) ? $info['CreateTime'] : 0;
        $data['MsgType'] = isset($info['MsgType']) ? $info['MsgType'] : '';
        $data['Content'] = isset($info['Content']) ? $info['Content'] : '';
        $data['MsgId'] = isset($info['MsgId']) ? $info['MsgId'] : '';
        $data['PicUrl'] = isset($info['PicUrl']) ? $info['PicUrl'] : '';
        $data['MediaId'] = isset($info['MediaId']) ? $info['MediaId'] : '';
        $data['Format'] = isset($info['Format']) ? $info['Format'] : '';
        $data['Recognition'] = isset($info['Recognition']) ? $info['Recognition'] : '';
        $data['ThumbMediaId'] = isset($info['ThumbMediaId']) ? $info['ThumbMediaId'] : '';
        $data['Location_X'] = isset($info['Location_X']) ? $info['Location_X'] : 0;
        $data['Location_Y'] = isset($info['Location_Y']) ? $info['Location_Y'] : 0;
        $data['Scale'] = isset($info['Scale']) ? $info['Scale'] : 0;
        $data['Label'] = isset($info['Label']) ? $info['Label'] : '';
        $data['AppType'] = isset($info['AppType']) ? $info['AppType'] : ''; //AppType app类型，在企业微信固定返回wxwork，在微信不返回该字段

        $data['Title'] = isset($info['Title']) ? $info['Title'] : '';
        $data['Description'] = isset($info['Description']) ? $info['Description'] : '';
        $data['Url'] = isset($info['Url']) ? $info['Url'] : '';
        $data['Event'] = isset($info['Event']) ? $info['Event'] : '';
        $data['EventKey'] = isset($info['EventKey']) ? $info['EventKey'] : '';
        $data['ChangeType'] = isset($info['ChangeType']) ? $info['ChangeType'] : ''; //ChangeType

        $data['MenuID'] = isset($info['MenuID']) ? $info['MenuID'] : '';
        $data['ScanCodeInfo'] = isset($info['ScanCodeInfo']) ? \App\Common\Utils\Helper::myJsonEncode($info['ScanCodeInfo']) : '';
        $data['SendPicsInfo'] = isset($info['SendPicsInfo']) ? \App\Common\Utils\Helper::myJsonEncode($info['SendPicsInfo']) : '';
        $data['SendLocationInfo'] = isset($info['SendLocationInfo']) ? \App\Common\Utils\Helper::myJsonEncode($info['SendLocationInfo']) : '';

        $data['Ticket'] = isset($info['Ticket']) ? $info['Ticket'] : '';
        $data['Latitude'] = isset($info['Latitude']) ? $info['Latitude'] : 0;
        $data['Longitude'] = isset($info['Longitude']) ? $info['Longitude'] : 0;
        $data['Precision'] = isset($info['Precision']) ? $info['Precision'] : 0;
        //发送任务卡片消息
        $data['TaskId'] = isset($info['TaskId']) ? $info['TaskId'] : ''; //TaskId 与发送任务卡片消息时指定的task_id相同

        $data['bizmsgmenuid'] = isset($info['bizmsgmenuid']) ? $info['bizmsgmenuid'] : '';

        $data['Status'] = isset($info['Status']) ? $info['Status'] : '';
        $data['TotalCount'] = isset($info['TotalCount']) ? $info['TotalCount'] : 0;
        $data['FilterCount'] = isset($info['FilterCount']) ? $info['FilterCount'] : 0;
        $data['SentCount'] = isset($info['SentCount']) ? $info['SentCount'] : 0;
        $data['ErrorCount'] = isset($info['ErrorCount']) ? $info['ErrorCount'] : 0;
        $data['CopyrightCheckResult'] = isset($info['CopyrightCheckResult']) ? \App\Common\Utils\Helper::myJsonEncode($info['CopyrightCheckResult']) : '';

        $data['CardId'] = isset($info['CardId']) ? $info['CardId'] : '';
        $data['RefuseReason'] = isset($info['RefuseReason']) ? $info['RefuseReason'] : '';
        $data['IsGiveByFriend'] = isset($info['IsGiveByFriend']) ? $info['IsGiveByFriend'] : 0;
        $data['FriendUserName'] = isset($info['FriendUserName']) ? $info['FriendUserName'] : '';
        $data['UserCardCode'] = isset($info['UserCardCode']) ? $info['UserCardCode'] : '';
        $data['OldUserCardCode'] = isset($info['OldUserCardCode']) ? $info['OldUserCardCode'] : '';
        $data['OuterStr'] = isset($info['OuterStr']) ? $info['OuterStr'] : '';
        $data['IsRestoreMemberCard'] = isset($info['IsRestoreMemberCard']) ? $info['IsRestoreMemberCard'] : 0;
        $data['UnionId'] = isset($info['UnionId']) ? $info['UnionId'] : '';
        $data['IsReturnBack'] = isset($info['IsReturnBack']) ? intval($info['IsReturnBack']) : 0;
        $data['IsChatRoom'] = isset($info['IsChatRoom']) ? intval($info['IsChatRoom']) : 0;
        $data['ConsumeSource'] = isset($info['ConsumeSource']) ? $info['ConsumeSource'] : '';
        $data['LocationName'] = isset($info['LocationName']) ? $info['LocationName'] : '';
        $data['StaffOpenId'] = isset($info['StaffOpenId']) ? $info['StaffOpenId'] : '';
        $data['VerifyCode'] = isset($info['VerifyCode']) ? $info['VerifyCode'] : '';
        $data['RemarkAmount'] = isset($info['RemarkAmount']) ? $info['RemarkAmount'] : '';
        $data['TransId'] = isset($info['TransId']) ? $info['TransId'] : '';
        $data['LocationId'] = isset($info['LocationId']) ? $info['LocationId'] : '';
        $data['Fee'] = isset($info['Fee']) ? $info['Fee'] : 0;
        $data['OriginalFee'] = isset($info['OriginalFee']) ? $info['OriginalFee'] : 0;
        $data['ModifyBonus'] = isset($info['ModifyBonus']) ? $info['ModifyBonus'] : 0;
        $data['ModifyBalance'] = isset($info['ModifyBalance']) ? $info['ModifyBalance'] : 0;
        $data['Detail'] = isset($info['Detail']) ? $info['Detail'] : '';
        $data['CreateOrderTime'] = isset($info['CreateOrderTime']) ? $info['CreateOrderTime'] : 0;
        $data['PayFinishTime'] = isset($info['PayFinishTime']) ? $info['PayFinishTime'] : 0;
        $data['Desc'] = isset($info['Desc']) ? $info['Desc'] : '';
        $data['FreeCoinCount'] = isset($info['FreeCoinCount']) ? $info['FreeCoinCount'] : 0;
        $data['PayCoinCount'] = isset($info['PayCoinCount']) ? $info['PayCoinCount'] : 0;
        $data['RefundFreeCoinCount'] = isset($info['RefundFreeCoinCount']) ? $info['RefundFreeCoinCount'] : 0;
        $data['RefundPayCoinCount'] = isset($info['RefundPayCoinCount']) ? $info['RefundPayCoinCount'] : 0;
        $data['OrderType'] = isset($info['OrderType']) ? $info['OrderType'] : '';
        $data['Memo'] = isset($info['Memo']) ? $info['Memo'] : '';
        $data['ReceiptInfo'] = isset($info['ReceiptInfo']) ? $info['ReceiptInfo'] : '';
        $data['PageId'] = isset($info['PageId']) ? $info['PageId'] : '';
        $data['OrderId'] = isset($info['OrderId']) ? $info['OrderId'] : '';
        $data['SuccOrderId'] = isset($info['SuccOrderId']) ? $info['SuccOrderId'] : '';
        $data['FailOrderId'] = isset($info['FailOrderId']) ? $info['FailOrderId'] : '';
        $data['AppId'] = isset($info['AppId']) ? $info['AppId'] : '';
        $data['Source'] = isset($info['Source']) ? $info['Source'] : '';

        $data['request_params'] = isset($info['request_params']) ? \App\Common\Utils\Helper::myJsonEncode($info['request_params']) : '';
        $data['request_xml'] = isset($info['request_xml']) ? ($info['request_xml']) : '';
        $data['response'] = isset($info['response']) ? ($info['response']) : '';
        $data['aes_info'] = isset($info['aes_info']) ? \App\Common\Utils\Helper::myJsonEncode($info['aes_info']) : '';
        $data['is_aes'] = isset($info['is_aes']) ? intval($info['is_aes']) : 0;
        $data['request_time'] = isset($info['request_time']) ? \App\Common\Utils\Helper::getCurrentTime($info['request_time']) : '';
        $data['response_time'] = isset($info['response_time']) ? \App\Common\Utils\Helper::getCurrentTime($info['response_time']) : '';
        $data['interval'] = isset($info['interval']) ? $info['interval'] : 0;

        $data['lock_uniqueKey'] = isset($info['lock_uniqueKey']) ? $info['lock_uniqueKey'] : '';
        $data['encrypt_response'] = isset($info['encrypt_response']) ? $info['encrypt_response'] : '';

        return $data;
    }
}
