<?php

namespace App\Lexiangla\Models\Contact;

class Department extends \App\Common\Models\Lexiangla\Contact\Department
{
    /**
     * 根据部门ID获取信息
     *
     * @param string $deptid         
     */
    public function getInfoByDepartmentId($deptid)
    {
        $query = array();
        $query['deptid'] = trim($deptid);

        $result = $this->findOne($query);
        return $result;
    }

    public function clearExist()
    {
        $updateData = array('is_exist' => 0);
        return $this->update(array('_id' => array('$gt' => '')), array('$set' => $updateData));
    }

    public function syncDepartmentList($departments, $now)
    {
        /**
         * {
         *  "code": 0,
         *  "msg": "ok",
         *  "data": {
         *      "id": 1,
         *      "name": "根部门",
         *      "parent_id": 0,
         *      "path": "/1",
         *      "order": 12354,
         *      "children": [
         *          {
         *              "id": 2,
         *              "name": "根部门",
         *              "parent_id": 1,
         *              "path": "/1/2",
         *              "order": 12356,
         *              "children": []
         *          }
         *      ]
         *  }
         * }
         */
        if (!empty($departments)) {
            foreach ($departments as $departmentInfo) {
                $deptid = $departmentInfo['id'];
                $info = $this->getInfoByDepartmentId($deptid);
                $data = array();
                $data['name'] = $departmentInfo['name'];
                $data['parent_id'] = $departmentInfo['parent_id'];
                $data['path'] = $departmentInfo['path'];
                $data['order'] = $departmentInfo['order'];
                $data['sync_time'] = \App\Common\Utils\Helper::getCurrentTime($now);
                // 通过这个字段来表明乐享那边有这条记录
                $data['is_exist'] = 1;
                if (!empty($info)) {
                    $this->update(array('_id' => $info['_id']), array('$set' => $data));
                } else {
                    $data['deptid'] = $deptid;
                    $this->insert($data);
                }
                // 如果有子部门的话那么就处理一下
                if (!empty($departmentInfo['children'])) {
                    $this->syncDepartmentList($departmentInfo['children'], $now);
                }
            }
        }
    }

    // 按乐享部门名获取乐享部门的信息
    public function getInfoByDepartmentName($name)
    {
        // 查找乐享标签的表记录
        $query = array();
        $query['name'] = trim($name);
        $query['is_exist'] = true;

        $result = $this->findOne($query);
        return $result;
    }

    // 如果部门名称或顺序发生了改变的话
    public function isDepartmentInfoChanged($qyweixinDepartmentInfo, $lexiangDepartmentInfo)
    {
        if (($lexiangDepartmentInfo['name'] != $qyweixinDepartmentInfo['name']) || ($lexiangDepartmentInfo['order'] != $qyweixinDepartmentInfo['order'])) {
            return true;
        }
        return false;
    }
}
