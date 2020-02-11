<?php

namespace App\Weixin2\Models\Menu;

class Conditional extends \App\Common\Models\Weixin2\Menu\Conditional
{

    /**
     * 获取匹配规则的列表信息
     *
     * @return array
     */
    public function getList4MatchRule($matchrule_id, $authorizer_appid, $component_appid)
    {
        $q = $this->getModel()->query();
        $q->where('matchrule', $matchrule_id);
        $q->where('authorizer_appid', $authorizer_appid);
        $q->where('component_appid', $component_appid);
        $q->orderby("priority", "asc")->orderby("id", "desc");
        $list = $q->get();
        $ret = array();
        if (!empty($list)) {
            foreach ($list as $item) {
                $item = $this->getReturnData($item);
                $ret[] = $item;
            }
        }
        return $ret;
    }

    /**
     * 构建菜单
     *
     * @return array
     */
    public function buildMenusWithMatchrule($ruleInfo, $authorizer_appid, $component_appid)
    {
        $matchrule_id = $ruleInfo['id'];
        unset($ruleInfo['id']);
        $menus = $this->getList4MatchRule($matchrule_id, $authorizer_appid, $component_appid);

        if (!empty($menus)) {
            $parent = array();
            $new = array();
            foreach ($menus as $a) {
                unset($a['authorizer_appid'], $a['component_appid'], $a['created_at'], $a['updated_at'], $a['deleted_at'], $a['matchrule']);
                if (empty($a['parent'])) {
                    $parent[] = $a;
                }
                $new['p_' . $a['parent']][] = $a;
            }
            $tree = $this->buildTree($new, $parent);
            return array(
                'button' => $tree,
                'matchrule' => $ruleInfo
            );
        } else {
            return array();
        }
    }

    public function recordMenuId($matchrule_id, $menuid, $menu_time)
    {
        $updateData = array();
        $updateData['menuid'] = $menuid;
        $updateData['menu_time'] = date("Y-m-d H:i:s", $menu_time);

        $updateModel = $this->getModel()->where("matchrule", $matchrule_id);
        return $this->update($updateModel, $updateData);
    }

    public function removeMenuId($matchrule_id, $menuid)
    {
        $updateData = array();
        $updateData['menuid'] = "";
        $updateModel = $this->getModel()
            ->where("matchrule", $matchrule_id)
            ->where("menuid", $menuid);
        return $this->update($updateModel, $updateData);
    }

    // /**
    // * 循环处理菜单
    // *
    // * @param array $menus
    // * @param array $parent
    // * @return array
    // */
    // private function buildTree(&$menus, $parent)
    // {
    // $tree = array();
    // foreach ($parent as $k => $l) {
    // $type = $l['type'];
    // if (isset($menus[$l['id']])) {
    // $l['sub_button'] = $this->buildTree($menus, $menus[$l['id']]);
    // unset($l['type'], $l['key'], $l['url'], $l['media_id'], $l['appid'], $l['pagepath'], $l['id']);
    // }
    // if (in_array($type, array(
    // 'media_id',
    // 'view_limited'
    // ))) {
    // // "type": "media_id",
    // // "name": "图片",
    // // "media_id": "MEDIA_ID1"
    // if (isset($l['key'])) {
    // unset($l['key']);
    // }
    // if (isset($l['url'])) {
    // unset($l['url']);
    // }
    // if (isset($l['appid'])) {
    // unset($l['appid']);
    // }
    // if (isset($l['pagepath'])) {
    // unset($l['pagepath']);
    // }
    // }
    // if ($type == 'view') {
    // // "type":"view",
    // // "name":"搜索",
    // // "url":"http://www.soso.com/"
    // if (isset($l['key'])) {
    // unset($l['key']);
    // }
    // if (isset($l['media_id'])) {
    // unset($l['media_id']);
    // }
    // if (isset($l['appid'])) {
    // unset($l['appid']);
    // }
    // if (isset($l['pagepath'])) {
    // unset($l['pagepath']);
    // }
    // }

    // if ($type == 'miniprogram') {
    // // "type":"miniprogram",
    // // "name":"wxa",
    // // "url":"http://mp.weixin.qq.com",
    // // "appid":"wx286b93c14bbf93aa",
    // // "pagepath":"pages/lunar/index"
    // if (isset($l['key'])) {
    // unset($l['key']);
    // }
    // if (isset($l['media_id'])) {
    // unset($l['media_id']);
    // }
    // }

    // if (in_array($type, array(
    // 'click',
    // 'scancode_push',
    // 'scancode_waitmsg',
    // 'pic_sysphoto',
    // 'pic_photo_or_album',
    // 'pic_weixin',
    // 'location_select'
    // ))) {
    // // "type": "pic_weixin",
    // // "name": "微信相册发图",
    // // "key": "rselfmenu_1_2"
    // if (isset($l['url'])) {
    // unset($l['url']);
    // }
    // if (isset($l['media_id'])) {
    // unset($l['media_id']);
    // }
    // if (isset($l['appid'])) {
    // unset($l['appid']);
    // }
    // if (isset($l['pagepath'])) {
    // unset($l['pagepath']);
    // }
    // }
    // unset($l['parent'], $l['priority'], $l['id']);
    // $tree[] = $l;
    // }
    // return $tree;
    // }

    /**
     * 循环处理菜单
     *
     * @param array $menus            
     * @param array $parent            
     * @return array
     */
    private function buildTree(&$menus, $parent)
    {
        $tree = array();
        foreach ($parent as $k => $l) {
            if (isset($menus['p_' . $l['id']])) {
                $l['sub_button'] = $this->buildTree($menus, $menus['p_' . $l['id']]);
            }
            $type = $l['type'];
            $item = array();
            if (!empty($l['sub_button'])) {
                $item['name'] = $l['name'];
                $item['sub_button'] = $l['sub_button'];
            } elseif (in_array($type, array(
                'media_id',
                'view_limited'
            ))) {
                // "type": "media_id",
                // "name": "图片",
                // "media_id": "MEDIA_ID1"
                $item["type"] = $l['type'];
                $item["name"] = $l['name'];
                $item["media_id"] = $l['media_id'];
            } elseif ($type == 'view') {
                // "type":"view",
                // "name":"搜索",
                // "url":"http://www.soso.com/"
                $item["type"] = $l['type'];
                $item["name"] = $l['name'];
                $item["url"] = $l['url'];
            } elseif ($type == 'miniprogram') {
                // "type":"miniprogram",
                // "name":"wxa",
                // "url":"http://mp.weixin.qq.com",
                // "appid":"wx286b93c14bbf93aa",
                // "pagepath":"pages/lunar/index"
                $item["type"] = $l['type'];
                $item["name"] = $l['name'];
                $item["url"] = $l['url'];
                $item["appid"] = $l['appid'];
                $item["pagepath"] = $l['pagepath'];
            } elseif (in_array($type, array(
                'click',
                'scancode_push',
                'scancode_waitmsg',
                'pic_sysphoto',
                'pic_photo_or_album',
                'pic_weixin',
                'location_select'
            ))) {
                // "type": "pic_weixin",
                // "name": "微信相册发图",
                // "key": "rselfmenu_1_2"
                $item["type"] = $l['type'];
                $item["name"] = $l['name'];
                $item["key"] = $l['key'];
            }
            if (!empty($item)) {
                $tree[] = $item;
            }
        }
        return $tree;
    }
}
