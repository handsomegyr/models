<?php

namespace App\Lexiangla\Models\Contact;

class DepartmentSync extends \App\Common\Models\Lexiangla\Contact\DepartmentSync
{
    // 按企业微信部门信息获取乐享部门同步的信息
    public function getInfoByQyDeptId($deptid)
    {
        // 查找乐享部门的同步表记录
        $query = array();
        $query['qyweixin_deptid'] = trim($deptid);

        $result = $this->findOne($query);
        return $result;
    }

    // 根据企业微信部门的父部门ID来获取乐享部门的父部门ID
    public function  getParentIdByQyDeptParentid($qyweixin_parentid)
    {
        // 如果是根部门的话 乐享的根部门id是1
        if (empty($qyweixin_parentid)) {
            $parent_id = 1;
        } else {
            // 查找乐享部门的同步表记录
            $info = $this->getInfoByQyDeptId($qyweixin_parentid);
            if (!empty($info)) {
                $parent_id = $info['deptid'];
            } else {
                $parent_id = 0;
            }
        }

        return $parent_id;
    }

    /**
     * 根据乐享部门id字符串获取企业微信部门id字符串
     *
     * @param string $deptid        乐享部门id
     * @param string $separator     分隔符
     * @return string
     */
    public function getQyDeptIdStringByDeptId($deptid = '', $separator = "|")
    {
        if ($deptid) {
            $arr = explode($separator, $deptid);
            $query = array();
            $query['deptid'] = array('$in' => $arr);
            $query['is_exist'] = true;
            $info = $this->findOne($query);
            if (empty($info)) {
                return '';
            }
            return implode($separator, $info['qyweixin_deptid']);
        } else {
            return '';
        }
    }
}
