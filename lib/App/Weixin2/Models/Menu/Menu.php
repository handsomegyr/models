<?php

namespace App\Weixin2\Models\Menu;

class Menu extends \App\Common\Models\Weixin2\Menu\Menu
{

    public function getMenus($authorizer_appid, $component_appid)
    {
        $ret = $this->findAll(array(
            'authorizer_appid' => $authorizer_appid,
            'component_appid' => $component_appid,
            'is_show' => 1
        ), array('priority' => 1, '_id' => 1));
        return $ret;
    }

    /**
     * 构建菜单
     *
     * @return array
     */
    public function buildMenu($authorizer_appid, $component_appid)
    {
        $menus = $this->getMenus($authorizer_appid, $component_appid);
        if (!empty($menus)) {
            $parent = array();
            $new = array();
            foreach ($menus as $a) {
                unset($a['authorizer_appid'], $a['component_appid'], $a['created_at'], $a['updated_at'], $a['deleted_at']);
                if (empty($a['parent'])) {
                    $parent[] = $a;
                }
                $new['p_' . $a['parent']][] = $a;
            }
            $tree = $this->buildTree($new, $parent);
            return array(
                'button' => $tree
            );
        } else {
            return array();
        }
    }

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
            if (isset($menus['p_' . $l['_id']])) {
                $l['sub_button'] = $this->buildTree($menus, $menus['p_' . $l['_id']]);
            }
            $type = $l['type'];
            $item = array();
            if (!empty($l['sub_button'])) {
                $item['name'] = $l['name'];
                $item['sub_button'] = $l['sub_button'];
            } elseif (in_array($type, array(
                'article_id',
                'article_view_limited'
            ))) {
                // "type": "article_id",
                // "name": "发布后的图文消息",
                // "article_id": "ARTICLE_ID1"
                $item["type"] = $l['type'];
                $item["name"] = $l['name'];
                $item["article_id"] = $l['article_id'];
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
