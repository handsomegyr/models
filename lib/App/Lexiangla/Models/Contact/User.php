<?php

namespace App\Lexiangla\Models\Contact;

class User extends \App\Common\Models\Lexiangla\Contact\User
{
    /**
     * 根据成员ID获取信息
     *
     * @param string $staff_id          
     */
    public function getInfoByStaffId($staff_id)
    {
        $query = array();
        $query['staff_id'] = trim($staff_id);

        $result = $this->findOne($query);
        return $result;
    }

    public function clearExist()
    {
        $updateData = array('is_exist' => 0);
        return $this->update(array('id' => array('$gt' => '')), array('$set' => $updateData));
    }

    public function syncUserList($res, $now)
    {
        /**
         * {
         *      "has_more": true,
         *      "user_list": [
         *          {
         *              "name": "张三",
         *              "gender": 0,
         *              "avatar": "https://static.lexiang-asset.net/build/img/avatar-default-54d6474725.png",
         *              "staff_id": "zhangsan",
         *              "departments": [
         *                  {
         *                      "id": "100",
         *                      "name": "开发组"
         *                  }
         *              ]
         *          },
         *          {
         *              "name": "李四",
         *              "gender": 1,
         *              "avatar": "https://static.lexiang-asset.net/build/img/avatar-default-54d6474725.png",
         *              "staff_id": "LX008",
         *              "departments": [
         *                  {
         *                      "id": "1",
         *                      "name": "深圳有限公司"
         *                  }
         *              ]
         *          }
         *      ],
         *      "msg": "success",
         *      "code": 0
         *  }
         */
        if (!empty($res['user_list'])) {
            foreach ($res['user_list'] as $userInfo) {
                $staff_id = $userInfo['staff_id'];
                if (empty($staff_id)) {
                    continue;
                }
                $info = $this->getInfoByStaffId($staff_id);
                $data = array();
                $data['name'] = $userInfo['name'];
                $data['gender'] = $userInfo['gender'];
                $data['avatar'] = $userInfo['avatar'];
                $data['departments'] = \App\Common\Utils\Helper::myJsonEncode($userInfo['departments']);
                $data['sync_time'] = \App\Common\Utils\Helper::getCurrentTime($now);
                // 通过这个字段来表明乐享那边有这条记录
                $data['is_exist'] = 1;
                if (!empty($info)) {
                    $this->update(array('_id' => $info['_id']), array('$set' => $data));
                } else {
                    $data['staff_id'] = $staff_id;
                    $this->insert($data);
                }
            }
        }
    }

    public function updateUserInfoById($id, $res, $now)
    {
        /**
         * {
         *  "data": {
         *      "id": "zhangsan",
         *      "name": "张三",
         *      "english_name": null,
         *      "gender": 2,
         *      "avatar": "https://wework.qpic.cn/bizmail/wicgNShU5sdhFgCdAHANpOkBwJZfUlWacrYVmgWw2Ip8fJygjTL382A/100",
         *      "position": "项目经理",
         *      "main_depart": "1",
         *      "department": [
         *          "项目部"
         *      ]
         *  },
         *  "msg": "success",
         *  "code": 0
         * }
         */
        $userInfo = $res['data'];
        $data = array();
        $data['name'] = $userInfo['name'];
        $data['english_name'] = empty($userInfo['english_name']) ? "" : $userInfo['english_name'];
        $data['gender'] = $userInfo['gender'];
        $data['avatar'] = $userInfo['avatar'];
        $data['position'] = $userInfo['position'];
        $data['main_depart'] = $userInfo['main_depart'];
        if (empty($userInfo['department'])) {
            $data['department'] = \App\Common\Utils\Helper::myJsonEncode(array());
        } else {
            $data['department'] = \App\Common\Utils\Helper::myJsonEncode(array_values(array_unique($userInfo['department'])));
        }

        $data['sync_time'] = \App\Common\Utils\Helper::getCurrentTime($now);
        // 通过这个字段来表明乐享那边有这条记录
        $data['is_exist'] = 1;
        $this->update(array('_id' => $id), array('$set' => $data));
    }

    // 按乐享成员名获取乐享成员的信息
    public function getInfoByUserName($name)
    {
        // 查找乐享成员的表记录
        $query = array();
        $query['name'] = trim($name);
        $query['is_exist'] = true;

        $result = $this->findOne($query);
        return $result;
    }

    // 如果成员发生了改变的话
    public function isUserInfoChanged($qyweixinUserInfo, $lexiangUserInfo, $main_depart, $department)
    {
        $qyweixin_gender = intval($qyweixinUserInfo['gender']);
        // 1表示男性，2表示女性。默认0表示未定义
        if ($qyweixin_gender != 1 && $qyweixin_gender != 2) {
            $qyweixin_gender = 0;
        }

        if (($lexiangUserInfo['name'] != $qyweixinUserInfo['name']) ||
            (!empty($qyweixinUserInfo['english_name']) && $lexiangUserInfo['english_name'] != $qyweixinUserInfo['english_name']) ||
            ($lexiangUserInfo['phone'] != $qyweixinUserInfo['mobile']) ||
            ($lexiangUserInfo['main_depart'] != $main_depart) ||
            (!empty($qyweixinUserInfo['position']) && $lexiangUserInfo['position'] != $qyweixinUserInfo['position']) ||
            (!empty($qyweixinUserInfo['external_position']) && $lexiangUserInfo['work_position'] != $qyweixinUserInfo['external_position']) ||
            ($lexiangUserInfo['gender'] != $qyweixin_gender) ||
            (!empty($qyweixinUserInfo['telephone']) && $lexiangUserInfo['tel_phone'] != $qyweixinUserInfo['telephone'])
        ) {
            return true;
        } else {
            $lexiangladepartment = $lexiangUserInfo['department'];
            if (!is_array($lexiangladepartment)) {
                $lexiangladepartment = \json_decode($lexiangladepartment, true);
            }
            if (empty($lexiangladepartment)) {
                $lexiangladepartment = array();
            }
            foreach ($lexiangladepartment as $item1) {
                if (!in_array($item1, $department)) {
                    return true;
                }
            }
            foreach ($department as $item2) {
                if (!in_array($item2, $lexiangladepartment)) {
                    return true;
                }
            }
        }
        return false;
    }
}
