<?php
namespace App\Weixin\Models;

class Menu extends \App\Common\Models\Weixin\Menu
{

    private $_weixin;

    private $_hookKey = '';

    /**
     * 设置微信对象
     */
    public function setWeixinInstance(\Weixin\Client $weixin)
    {
        $this->_weixin = $weixin;
    }

    /**
     * 构建菜单
     *
     * @return array
     */
    public function buildMenu()
    {
        $menus = $this->findAll(array(), array(
            'priority' => - 1
        ), array(
            '_id' => true,
            'parent' => true,
            'type' => true,
            'name' => true,
            'key' => true,
            'url' => true
        ));
        if (! empty($menus)) {
            $menus = convertToPureArray($menus);
            $parent = array();
            $new = array();
            foreach ($menus as $a) {
                if (empty($a['parent']))
                    $parent[] = $a;
                $new[$a['parent']][] = $a;
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
            $type = $l['type'];
            if (isset($menus[$l['_id']])) {
                $l['sub_button'] = $this->buildTree($menus, $menus[$l['_id']]);
                unset($l['type'], $l['key'], $l['url'], $l['media_id'], $l['appid'], $l['pagepath'], $l['_id']);
            }
            if (in_array($type, array(
                'media_id',
                'view_limited'
            ))) {
                // "type": "media_id",
                // "name": "图片",
                // "media_id": "MEDIA_ID1"
                if (isset($l['key'])) {
                    unset($l['key']);
                }
                if (isset($l['url'])) {
                    unset($l['url']);
                }
                if (isset($l['appid'])) {
                    unset($l['appid']);
                }
                if (isset($l['pagepath'])) {
                    unset($l['pagepath']);
                }
            }
            if ($type == 'view') {
                // "type":"view",
                // "name":"搜索",
                // "url":"http://www.soso.com/"
                if (isset($l['key'])) {
                    unset($l['key']);
                }
                if (isset($l['media_id'])) {
                    unset($l['media_id']);
                }
                if (isset($l['appid'])) {
                    unset($l['appid']);
                }
                if (isset($l['pagepath'])) {
                    unset($l['pagepath']);
                }
            }
            
            if ($type == 'miniprogram') {
                // "type":"miniprogram",
                // "name":"wxa",
                // "url":"http://mp.weixin.qq.com",
                // "appid":"wx286b93c14bbf93aa",
                // "pagepath":"pages/lunar/index"
                if (isset($l['key'])) {
                    unset($l['key']);
                }
                if (isset($l['media_id'])) {
                    unset($l['media_id']);
                }
            }
            
            if (in_array($type, array(
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
                if (isset($l['url'])) {
                    unset($l['url']);
                }
                if (isset($l['media_id'])) {
                    unset($l['media_id']);
                }
                if (isset($l['appid'])) {
                    unset($l['appid']);
                }
                if (isset($l['pagepath'])) {
                    unset($l['pagepath']);
                }
            }
            unset($l['parent'], $l['priority'], $l['_id']);
            $tree[] = $l;
        }
        return $tree;
    }
}