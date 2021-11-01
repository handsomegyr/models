<?php

namespace App\Lexiangla\Models\Contact;

class TagParty extends \App\Common\Models\Lexiangla\Contact\TagParty
{
    /**
     * 根据部门ID和标签ID获取信息
     *
     * @param string $deptid   
     * @param string $tagid          
     * @param string $authorizer_appid          
     */
    public function getInfoByDeptidAndTagid($deptid, $tagid)
    {
        $query = array();
        $query['deptid'] = trim($deptid);
        $query['tagid'] = trim($tagid);

        $result = $this->findOne($query);
        return $result;
    }

    public function clearExist($tagid)
    {
        $updateData = array('is_exist' => false);
        return $this->update(array('tagid' => $tagid), array('$set' => $updateData));
    }

    public function syncTagDepartmentList($tagid, $res, $now)
    {
        $this->clearExist($tagid);
        /**
         * {
         *      "msg": "success",
         *      "code": 0,
         *      "data": {
         *          "department_list": [
         *          ],
         *          "name": "标签A",
         *          "id": "572c2858fe6f11ebb1d2a67595a2469f",
         *          "user_list": [
         *              "xiaoming",
         *              "zhangshan",
         *              "lisi"
         *          ]
         *      }
         *  }
         */
        if (!empty($res['data']['department_list'])) {
            foreach ($res['data']['department_list'] as $deptid) {
                $info = $this->getInfoByDeptidAndTagid($deptid, $tagid);
                $data = array();
                $data['tagname'] = isset($res['name']) ? $res['name'] : "";
                $data['is_exist'] = 1;
                $data['get_time'] = \App\Common\Utils\Helper::getCurrentTime($now);
                $data['memo'] = \App\Common\Utils\Helper::myJsonEncode($res);
                if (!empty($info)) {
                    $this->update(array('_id' => $info['_id']), array('$set' => $data));
                } else {
                    $data['deptid'] = $deptid;
                    $data['tagid'] = $tagid;
                    $this->insert($data);
                }
            }
        }
    }
}
