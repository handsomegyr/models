<?php

namespace App\Qyweixin\Models\ExternalContact;

class ExternalUserFollowUser extends \App\Common\Models\Qyweixin\ExternalContact\ExternalUserFollowUser
{
    /**
     * 根据客户ID获取信息
     *
     * @param string $external_userid 
     * @param string $userid              
     * @param string $authorizer_appid          
     */
    public function getInfoByUserId($external_userid, $userid, $authorizer_appid)
    {
        $query = array();
        $query['external_userid'] = $external_userid;
        $query['userid'] = $userid;
        $query['authorizer_appid'] = $authorizer_appid;
        $info = $this->findOne($query);

        return $info;
    }

    public function syncFollowUserList($external_userid, $authorizer_appid, $provider_appid, $res, $now)
    {
        /**
         * {
         *"userid":"rocky",
         *"remark":"李部长",
         *"description":"对接采购事务",
         *"createtime":1525779812,
         *"tags":
         *[
         *    {
         *     "group_name":"标签分组名称",
         *     "tag_name":"标签名称",
         *      "type":1
         *      }
         *],
         *"remark_corp_name":"腾讯科技",
         *"remark_mobiles":
         *[
         *    "13800000001",
         *    "13000000002"
         *],
         *"oper_userid":"rocky",
         *"add_way":1
         *},
         */
        if (!empty($res['follow_user'])) {
            foreach ($res['follow_user'] as $useridInfo) {

                $userid = isset($useridInfo['userid']) ? $useridInfo['userid'] : '';
                $data = array();
                $data['provider_appid'] = $provider_appid;
                $data['get_time'] = \App\Common\Utils\Helper::getCurrentTime($now);
                $data['remark'] = isset($useridInfo['remark']) ? $useridInfo['remark'] : '';
                $data['description'] = isset($useridInfo['description']) ? $useridInfo['description'] : '';
                $data['createtime'] = \App\Common\Utils\Helper::getCurrentTime($useridInfo['createtime']);
                $data['remark_corp_name'] = isset($useridInfo['remark_corp_name']) ? $useridInfo['remark_corp_name'] : '';
                $data['tags'] = isset($useridInfo['tags']) ? \App\Common\Utils\Helper::myJsonEncode($useridInfo['tags']) : '';
                $data['remark_mobiles'] = isset($useridInfo['remark_mobiles']) ? \App\Common\Utils\Helper::myJsonEncode($useridInfo['remark_mobiles']) : '';
                $data['oper_userid'] = isset($useridInfo['oper_userid']) ? \App\Common\Utils\Helper::myJsonEncode($useridInfo['oper_userid']) : '';
                $data['add_way'] = isset($useridInfo['add_way']) ? intval($useridInfo['add_way']) : 0;
                $data['state'] = isset($useridInfo['state']) ? $useridInfo['state'] : '';

                $info = $this->getInfoByUserId($external_userid, $userid, $authorizer_appid);

                if (!empty($info)) {
                    $this->update(array('_id' => $info['_id']), array('$set' => $data));
                } else {
                    $data['authorizer_appid'] = $authorizer_appid;
                    $data['external_userid'] = $external_userid;
                    $data['userid'] = $userid;
                    $this->insert($data);
                }
            }
        }
    }
}
