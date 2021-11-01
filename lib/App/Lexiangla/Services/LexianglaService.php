<?php

namespace App\Lexiangla\Services;

class LexianglaService
{
    private $appKey = "";

    private $appSecret = "";

    private $objLxapi = null;

    /**
     *
     * @var \App\Lexiangla\Models\Application
     */
    private $modelApplication;

    public function __construct($appKey, $appSecret)
    {
        $this->appKey = $appKey;
        $this->appSecret = $appSecret;
        $this->objLxapi = new \Lexiangla\Openapi\Api($this->appKey, $this->appSecret);
        $this->modelApplication = new \App\Lexiangla\Models\Application();
    }

    /**
     * @var \Lexiangla\Openapi\Api
     */
    public function getLxapiObject()
    {
        $this->getAccessToken();
        return $this->objLxapi;
    }

    public function getAccessToken()
    {
        $applicationInfo = $this->modelApplication->getTokenByAppid($this->appKey);
        if (empty($applicationInfo)) {
            throw new \Exception("appKey:{$this->appKey}的记录不存在");
        }
        $access_token = $applicationInfo['access_token'];
        $this->objLxapi->setAccessToken($access_token);
        return $access_token;
    }

    public function getApplicationInfo()
    {
        $applicationInfo = $this->modelApplication->getInfoByAppid($this->appKey, false);
        if (empty($applicationInfo)) {
            throw new \Exception("appKey:{$this->appKey}的记录不存在");
        }
        return $applicationInfo;
    }

    // 获取部门列表
    public function getDepartmentList($dep_id)
    {
        $modelDepartment = new \App\Lexiangla\Models\Contact\Department();

        // 调用乐享的获取部门列表接口
        $params = array(
            "id" => $dep_id,
            "with_descendant" => 1,
        );
        $res = $this->getLxapiObject()->get("contact/department/index", $params);
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
        // $params['res'] = $res;
        if (empty($res)) {
            throw new \Exception("获取部门列表失败,无返回值:" . \App\Common\Utils\Helper::myJsonEncode($params));
        }
        if (!isset($res['code'])) {
            throw new \Exception("获取部门列表失败,无code返回值:" . \App\Common\Utils\Helper::myJsonEncode(array('res' => $res, 'params' => $params)));
        }
        if (!empty($res['code'])) {
            throw new \Exception($res['msg'], $res['code']);
        }

        // 如果从跟部门进行同步的话 那么先将所有的记录is_exist改成0
        if (intval($dep_id) == 1) {
            $modelDepartment->clearExist();
        }
        if (!empty($res['data'])) {
            $modelDepartment->syncDepartmentList(array($res['data']), time());
        }
        return $res;
    }

    // 获取标签列表
    public function getTagList()
    {
        $modelTag = new \App\Lexiangla\Models\Contact\Tag();

        // 调用乐享的获取标签列表接口
        $params = array(
            'offset' => 0,
            'limit' => 10000,
        );
        $res = $this->getLxapiObject()->get("contact/tag/all", $params);
        /**
         * {
         *      "msg": "success",
         *      "code": 0,
         *      "data": {
         *          "total": 2,
         *          "list": [
         *              {
         *                  "name": "标签A",
         *                  "id": "572c2858fe6f11ebb1d2a67595a2469f"
         *              },
         *              {
         *                  "name": "标签B",
         *                  "id": "954b52c0fe7011eb9f34d229e73ba5ca"
         *              }
         *          ]
         *      }
         *  }
         */
        // $params['res'] = $res;
        if (empty($res)) {
            throw new \Exception("获取标签列表失败,无返回值:" . \App\Common\Utils\Helper::myJsonEncode($params));
        }
        if (!isset($res['code'])) {
            throw new \Exception("获取标签列表失败,无code返回值:" . \App\Common\Utils\Helper::myJsonEncode(array('res' => $res, 'params' => $params)));
        }
        if (!empty($res['code'])) {
            throw new \Exception($res['msg'], $res['code']);
        }

        $modelTag->syncTagList($res, time());
        return $res;
    }

    // 获取标签成员
    public function getTag($tag_id)
    {
        // 调用乐享的获取获取标签成员接口
        $params = array(
            'tag_id' => $tag_id
        );
        $res = $this->getLxapiObject()->get("contact/tag/users", $params);
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
        // $params['res'] = $res;
        if (empty($res)) {
            throw new \Exception("获取获取标签成员失败,无返回值:" . \App\Common\Utils\Helper::myJsonEncode($params));
        }
        if (!isset($res['code'])) {
            throw new \Exception("获取获取标签成员失败,无code返回值:" . \App\Common\Utils\Helper::myJsonEncode(array('res' => $res, 'params' => $params)));
        }
        if (!empty($res['code'])) {
            throw new \Exception($res['msg'], $res['code']);
        }

        $modelTagParty = new \App\Lexiangla\Models\Contact\TagParty();
        $modelTagUser = new \App\Lexiangla\Models\Contact\TagUser();
        $now = time();
        $modelTagParty->syncTagDepartmentList($tag_id, $res, $now);
        $modelTagUser->syncTagUserList($tag_id, $res, $now);
        return $res;
    }

    // 获取成员列表
    public function getUserList()
    {
        $modelUser = new \App\Lexiangla\Models\Contact\User();

        // 调用乐享的获取成员列表接口
        $page = 1;
        $user_list = array();
        do {
            $params = array(
                'page' => $page,
                'per_page' => 100,
                'department_id' => 1,
                'fetch_child' => 1,
            );
            $res = $this->getLxapiObject()->get("contact/user/list", $params);
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
            // $params['res'] = $res;
            if (empty($res)) {
                throw new \Exception("获取成员列表失败,无返回值:" . \App\Common\Utils\Helper::myJsonEncode($params));
            }
            if (!isset($res['code'])) {
                throw new \Exception("获取成员列表失败,无code返回值:" . \App\Common\Utils\Helper::myJsonEncode(array('res' => $res, 'params' => $params)));
            }
            if (!empty($res['code'])) {
                throw new \Exception($res['msg'], $res['code']);
            }

            if (!empty($res['user_list'])) {
                $user_list = array_merge($user_list, $res['user_list']);
            }
            $res['user_list'] = $user_list;
            // 如果还有的话
            if (!empty($res['has_more'])) {
                $page++;
            } else {
                break;
            }
            if ($page > 10000) {
                break;
            }
        } while ($page <= 10000);

        $modelUser->clearExist();
        $modelUser->syncUserList($res, time());
        return $res;
    }

    // 读取成员
    public function getUserInfo($userInfo)
    {
        $modelUser = new \App\Lexiangla\Models\Contact\User();

        // 调用乐享的获取成员列表接口
        $params = array(
            'staff_id' => $userInfo['staff_id']
        );
        $res = $this->getLxapiObject()->get("contact/user/get", $params);
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
        // $params['res'] = $res;
        if (empty($res)) {
            throw new \Exception("获取成员失败,无返回值:" . \App\Common\Utils\Helper::myJsonEncode($params));
        }
        if (!isset($res['code'])) {
            throw new \Exception("获取成员失败,无code返回值:" . \App\Common\Utils\Helper::myJsonEncode(array('res' => $res, 'params' => $params)));
        }
        if (!empty($res['code'])) {
            throw new \Exception($res['msg'], $res['code']);
        }
        $modelUser->updateUserInfoById($userInfo['id'], $res, time());
        return $res;
    }
}
