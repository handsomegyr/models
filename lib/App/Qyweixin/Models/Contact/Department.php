<?php

namespace App\Qyweixin\Models\Contact;

class Department extends \App\Common\Models\Qyweixin\Contact\Department
{

    /**
     * 根据部门ID获取信息
     *
     * @param string $deptid            
     * @param string $authorizer_appid          
     */
    public function getInfoByDepartmentId($deptid, $authorizer_appid)
    {
        $query = array();
        $query['deptid'] = $deptid;
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

    public function syncDepartmentList($authorizer_appid, $provider_appid, $res, $now)
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
         *      "parentid": 1,
         *      "order": 10
         *  },
         *  {
         *      "id": 3,
         *      "name": "邮箱产品部",
         *      "name_en": "mail",
         *      "parentid": 2,
         *      "order": 40
         *  }
         *]
         * }
         */
        if (!empty($res['department'])) {
            foreach ($res['department'] as $departmentInfo) {
                $deptid = $departmentInfo['id'];
                $info = $this->getInfoByDepartmentId($deptid, $authorizer_appid);
                $data = array();
                $data['provider_appid'] = $provider_appid;
                $data['name'] = $departmentInfo['name'];
                if (isset($departmentInfo['name_en'])) {
                    $data['name_en'] = $departmentInfo['name_en'];
                }
                $data['parentid'] = $departmentInfo['parentid'];
                $data['order'] = $departmentInfo['order'];
                $data['sync_time'] = \App\Common\Utils\Helper::getCurrentTime($now);
                if (!empty($info)) {
                    $this->update(array('_id' => $info['_id']), array('$set' => $data));
                } else {
                    $data['authorizer_appid'] = $authorizer_appid;
                    $data['deptid'] = $deptid;
                    $this->insert($data);
                }
            }
        }
    }
}
