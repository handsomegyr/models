<?php

namespace App\Weixin2\Models\Msg;

class Log extends \App\Common\Models\Weixin2\Msg\Log
{

    protected $changeToArrayFields = array(
        'request_params',
        'aes_info'
    );

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
        $data['AgentID'] = isset($info['AgentID']) ? $info['AgentID'] : 0; //企业微信 企业应用的id，整型。可在应用的设置页面查看        
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
        $data['ScanCodeInfo'] = isset($info['ScanCodeInfo']) ? \json_encode($info['ScanCodeInfo']) : '';
        $data['SendPicsInfo'] = isset($info['SendPicsInfo']) ? \json_encode($info['SendPicsInfo']) : '';
        $data['SendLocationInfo'] = isset($info['SendLocationInfo']) ? \json_encode($info['SendLocationInfo']) : '';

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
        $data['CopyrightCheckResult'] = isset($info['CopyrightCheckResult']) ? \json_encode($info['CopyrightCheckResult']) : '';

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

        //成员事件
        $data['contact_UserID'] = isset($info['UserID']) ? $info['UserID'] : ''; // UserID 成员UserID
        $data['contact_NewUserID'] = isset($info['NewUserID']) ? $info['NewUserID'] : ''; // NewUserID 新的UserID，变更时推送（userid由系统生成时可更改一次）
        $data['contact_Name'] = isset($info['Name']) ? $info['Name'] : ''; // Name 成员名称
        $data['contact_Department'] = isset($info['Department']) ? ($info['Department']) : ''; // Department 成员部门列表，仅返回该应用有查看权限的部门id
        $data['contact_IsLeaderInDept'] = isset($info['IsLeaderInDept']) ?  ($info['IsLeaderInDept']) : ''; // IsLeaderInDept 表示所在部门是否为上级，0-否，1-是，顺序与Department字段的部门逐一对应
        $data['contact_Mobile'] = isset($info['Mobile']) ? $info['Mobile'] : ''; // Mobile 手机号码
        $data['contact_Position'] = isset($info['Position']) ? $info['Position'] : ''; // Position 职位信息。长度为0~64个字节
        $data['contact_Gender'] = isset($info['Gender']) ? $info['Gender'] : ''; // Gender 性别，1表示男性，2表示女性
        $data['contact_Email'] = isset($info['Email']) ? $info['Email'] : ''; // Email 邮箱
        $data['contact_Status'] = isset($info['Status']) ? $info['Status'] : ''; // Status 激活状态：1=已激活 2=已禁用 4=未激活 已激活代表已激活企业微信或已关注微工作台（原企业号）。
        $data['contact_Avatar'] = isset($info['Avatar']) ? $info['Avatar'] : ''; // Avatar 头像url。注：如果要获取小图将url最后的”/0”改成”/100”即可。
        $data['contact_Alias'] = isset($info['Alias']) ? $info['Alias'] : ''; // Alias 成员别名
        $data['contact_Telephone'] = isset($info['Telephone']) ? $info['Telephone'] : ''; // Telephone 座机
        $data['contact_Address'] = isset($info['Address']) ? $info['Address'] : ''; // Address 地址
        $data['contact_ExtAttr'] = isset($info['ExtAttr']) ? \json_encode($info['ExtAttr']) : ''; // ExtAttr 扩展属性

        //部门事件
        $data['contact_DeptId'] = isset($info['Id']) ? $info['Id'] : ''; // Id 部门Id
        $data['contact_DeptName'] = isset($info['Name']) ? $info['Name'] : ''; // Name 部门名称
        $data['contact_DeptParentId'] = isset($info['ParentId']) ? $info['ParentId'] : ''; // ParentId 父部门id
        $data['contact_DeptOrder'] = isset($info['Order']) ? $info['Order'] : 0; // Order 部门排序

        // 标签成员变更事件
        $data['contact_TagId'] = isset($info['TagId']) ? $info['TagId'] : ''; // TagId 标签Id
        $data['contact_AddUserItems'] = isset($info['AddUserItems']) ? $info['AddUserItems'] : ''; // AddUserItems 标签中新增的成员userid列表，用逗号分隔
        $data['contact_DelUserItems'] = isset($info['DelUserItems']) ? $info['DelUserItems'] : ''; // DelUserItems 标签中删除的成员userid列表，用逗号分隔
        $data['contact_AddPartyItems'] = isset($info['AddPartyItems']) ? $info['AddPartyItems'] : ''; // AddPartyItems 标签中新增的部门id列表，用逗号分隔
        $data['contact_DelPartyItems'] = isset($info['DelPartyItems']) ? $info['DelPartyItems'] : ''; // DelPartyItems 标签中删除的部门id列表，用逗号分隔

        //异步任务完成通知
        $data['BatchJob'] = isset($info['BatchJob']) ? \json_encode($info['BatchJob']) : ''; // BatchJob

        //企业客户事件
        $data['external_contact_UserID'] = isset($info['UserID']) ? $info['UserID'] : ''; // UserID 企业服务人员的UserID
        $data['external_contact_ExternalUserID'] = isset($info['ExternalUserID']) ? $info['ExternalUserID'] : ''; // ExternalUserID 外部联系人的userid，注意不是企业成员的帐号
        $data['external_contact_State'] = isset($info['State']) ? $info['State'] : ''; // State 添加此用户的「联系我」方式配置的state参数，可用于识别添加此用户的渠道
        $data['external_contact_WelcomeCode'] = isset($info['WelcomeCode']) ? $info['WelcomeCode'] : ''; // WelcomeCode 欢迎语code，可用于发送欢迎语

        //客户群变更事件
        $data['ChatId'] = isset($info['ChatId']) ? $info['ChatId'] : ''; // ChatId 群ID

        //审批状态通知事件 和 审批申请状态变化回调通知
        $data['ApprovalInfo'] = isset($info['ApprovalInfo']) ? \json_encode($info['ApprovalInfo']) : ''; // ApprovalInfo 审批信息

        $data['request_params'] = isset($info['request_params']) ? \json_encode($info['request_params']) : '';
        $data['request_xml'] = isset($info['request_xml']) ? ($info['request_xml']) : '';
        $data['response'] = isset($info['response']) ? ($info['response']) : '';
        $data['aes_info'] = isset($info['aes_info']) ? \json_encode($info['aes_info']) : '';
        $data['is_aes'] = isset($info['is_aes']) ? intval($info['is_aes']) : 0;
        $data['request_time'] = isset($info['request_time']) ? \App\Common\Utils\Helper::getCurrentTime($info['request_time']) : '';
        $data['response_time'] = isset($info['response_time']) ? \App\Common\Utils\Helper::getCurrentTime($info['response_time']) : '';
        $data['interval'] = isset($info['interval']) ? $info['interval'] : 0;

        $data['lock_uniqueKey'] = isset($info['lock_uniqueKey']) ? $info['lock_uniqueKey'] : '';
        $data['encrypt_response'] = isset($info['encrypt_response']) ? $info['encrypt_response'] : '';

        return $data;
    }
}
