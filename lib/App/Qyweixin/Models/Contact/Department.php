<?php

namespace App\Qyweixin\Models\Contact;

class Department extends \App\Common\Models\Qyweixin\Contact\Department
{

    /**
     * 根据部门ID获取信息
     *
     * @param string $deptid
     * @param string $authorizer_appid
     * @param string $provider_appid
     * @param string $agentid
     */
    public function getInfoByDepartmentId($deptid, $authorizer_appid, $provider_appid, $agentid)
    {
        $query = array();
        $query['deptid'] = $deptid;
        $query['agentid'] = $agentid;
        $query['authorizer_appid'] = $authorizer_appid;
        $query['provider_appid'] = $provider_appid;
        $info = $this->findOne($query);
        return $info;
    }

    public function clearExist($authorizer_appid, $provider_appid, $agentid, $now)
    {
        $updateData = array('is_exist' => 0);
        $updateData['sync_time'] = \App\Common\Utils\Helper::getCurrentTime($now);
        return $this->update(
            array(
                'agentid' => $agentid,
                'authorizer_appid' => $authorizer_appid,
                'provider_appid' => $provider_appid
            ),
            array('$set' => $updateData)
        );
    }

    public function syncDepartmentList($authorizer_appid, $provider_appid, $agentid, $res, $now)
    {
        /**
         * {
         * "errcode": 0,
         * "errmsg": "ok",
         * "department": [
         *  {
         *      "id": 2,
         *      "name": "广州研发中心",
         *      "name_en": "RDGZ",
         *      "department_leader":["zhangsan","lisi"],
         *      "parentid": 1,
         *      "order": 10
         *  },
         *  {
         *      "id": 3,
         *      "name": "邮箱产品部",
         *      "name_en": "mail",
         *      "department_leader":["zhangsan","lisi"],
         *      "parentid": 2,
         *      "order": 40
         *  }
         *]
         * }
         */
        if (!empty($res['department'])) {
            foreach ($res['department'] as $departmentInfo) {
                $deptid = $departmentInfo['id'];
                $info = $this->getInfoByDepartmentId($deptid, $authorizer_appid, $provider_appid, $agentid);
                $data = array();
                $data['name'] = $departmentInfo['name'];
                if (isset($departmentInfo['name_en'])) {
                    $data['name_en'] = $departmentInfo['name_en'];
                }
                if (!empty($departmentInfo['department_leader'])) {
                    $data['department_leader'] = \App\Common\Utils\Helper::myJsonEncode($departmentInfo['department_leader']);
                }
                $data['parentid'] = $departmentInfo['parentid'];
                $data['order'] = $departmentInfo['order'];
                $data['sync_time'] = \App\Common\Utils\Helper::getCurrentTime($now);
                // 通过这个字段来表明企业微信那边有这条记录
                $data['is_exist'] = 1;
                if (!empty($info)) {
                    $this->update(array('_id' => $info['_id']), array('$set' => $data));
                } else {
                    $data['provider_appid'] = $provider_appid;
                    $data['authorizer_appid'] = $authorizer_appid;
                    $data['agentid'] = $agentid;
                    $data['deptid'] = $deptid;
                    $this->insert($data);
                }
            }
        }
    }
}
