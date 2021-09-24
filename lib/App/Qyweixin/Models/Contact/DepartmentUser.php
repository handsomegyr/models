<?php

namespace App\Qyweixin\Models\Contact;

class DepartmentUser extends \App\Common\Models\Qyweixin\Contact\DepartmentUser
{

    /**
     * 根据部门ID获取信息
     *
     * @param string $userid   
     * @param string $deptid          
     * @param string $authorizer_appid          
     */
    public function getInfoByUserIdAndDepartmentId($userid, $department_id, $authorizer_appid)
    {
        $query = array();
        $query['userid'] = $userid;
        $query['department_id'] = $department_id;
        $query['authorizer_appid'] = $authorizer_appid;
        $info = $this->findOne($query);
        return $info;
    }

    public function clearExist($authorizer_appid, $provider_appid)
    {
        $updateData = array('is_exist' => 0);
        return $this->update(
            array(
                'authorizer_appid' => $authorizer_appid,
                'provider_appid' => $provider_appid
            ),
            array('$set' => $updateData)
        );
    }

    public function syncDepartmentUserList($authorizer_appid, $provider_appid, $res, $now)
    {
        /**
         * {
         * "errcode": 0,
         * "errmsg": "ok",
         * "userlist": [
         * {
         *    "userid": "zhangsan",
         *    "name": "李四",
         *    "department": [1, 2],
         *    "open_userid": "xxxxxx"
         * }
         *]
         * }
         */
        if (!empty($res['userlist'])) {
            foreach ($res['userlist'] as $departmentUserInfo) {
                if (!empty($departmentUserInfo['department'])) {
                    foreach ($departmentUserInfo['department'] as $department_id) {
                        $userid = $departmentUserInfo['userid'];
                        $info = $this->getInfoByUserIdAndDepartmentId($userid, $department_id, $authorizer_appid);
                        $data = array();
                        $data['provider_appid'] = $provider_appid;
                        $data['name'] = $departmentUserInfo['name'];
                        if (isset($departmentUserInfo['department'])) {
                            $data['department'] = \\App\Common\Utils\Helper::myJsonEncode($departmentUserInfo['department']);
                        }
                        if (isset($departmentUserInfo['open_userid'])) {
                            $data['open_userid'] = $departmentUserInfo['open_userid'];
                        }
                        $data['is_exist'] = 1;
                        $data['get_time'] = \App\Common\Utils\Helper::getCurrentTime($now);
                        if (!empty($info)) {
                            $this->update(array('_id' => $info['_id']), array('$set' => $data));
                        } else {
                            $data['authorizer_appid'] = $authorizer_appid;
                            $data['userid'] = $userid;
                            $data['department_id'] = $department_id;
                            $this->insert($data);
                        }
                    }
                }
            }
        }
    }
}
