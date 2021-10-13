<?php

namespace App\Backend\Submodules\Backend\Models;

use App\Backend\Models\Input;

class Menu extends \App\Common\Models\Backend\Menu
{
    use \App\Backend\Models\Base;

    /**
     * 默认排序
     */
    public function getDefaultSort()
    {
        $sort = array(
            'show_order' => 1,
            '_id' => -1
        );
        return $sort;
    }

    /**
     * 默认查询条件
     */
    public function getQuery()
    {
        $query = array(
            'is_show' => true
        );
        return $query;
    }

    /**
     * 获取商品菜单列表信息
     *
     * @param Input $input            
     * @return array
     */
    public function getList(Input $input)
    {
        // 分页查询
        $sort = $this->getDefaultSort();
        $list = $this->findAll($input->getQuery(), $sort);

        $menuList = array();
        if (!empty($list)) {
            foreach ($list as $item) {
                $pkey = "p:" . (empty($item['pid']) ? "0" : $item['pid']);
                $key = ($item['_id']);
                $menuList[$pkey][$key] = $item;
            }
        }
        if (!empty($menuList["p:0"])) {
            $input->setRecordCount(count($menuList["p:0"]));
            $filter = $input->getFilter();
            $menuList["p:0"] = array_slice($menuList["p:0"], $filter['start'], min($filter['record_count'], $filter['page_size']));
            $datas = $this->recursiveGet($menuList, "0", 0);
        } else {
            $input->setRecordCount(0);
            $filter = $input->getFilter();
            $datas = array();
        }

        return array(
            'data' => $datas,
            'filter' => $filter,
            'page_count' => $filter['page_count'],
            'record_count' => $filter['record_count']
        );
    }

    /**
     * 递归方式获取信息
     *
     * @param array $menuList            
     * @param string $pkey            
     * @param int $level            
     * @return Ambigous <multitype:, multitype:number >
     */
    private function recursiveGet($menuList, $pkey, $level = 0)
    {
        $list = array();
        $pkey = "p:" . $pkey;
        if (!empty($menuList[$pkey])) {
            foreach ($menuList[$pkey] as $key => $item) {
                $item['level'] = $level;
                $item['menu_id'] = $item['_id'];
                $item['has_children'] = (empty($menuList["p:" . $item['menu_id']])) ? 0 : 1;

                $list[] = $item;
                $list2 = $this->recursiveGet($menuList, $key, $level + 1);
                if (!empty($list2)) {
                    $list = array_merge($list, $list2);
                }
            }
        }
        return $list;
    }

    /**
     * 检查名称是否存在
     *
     * @param string $id            
     * @param string $pid            
     * @param string $name            
     * @throws Exception
     */
    public function checkName($id, $pid, $name)
    {
        /* 同级别下不能有重复的菜单名称 */
        $query = array();
        $query['name'] = urldecode($name);
        if (!empty($id)) {
            $query['_id'] = array(
                '$ne' => $id
            );
        }
        $query['pid'] = $pid;
        $num = $this->count($query);
        if ($num > 0) {
            throw new \Exception(sprintf('已存在相同的菜单名称!', stripslashes($name)), 1);
        }
    }

    /**
     * 是否是页子节点
     *
     * @param string $id            
     * @throws Exception
     */
    public function checkIsLeaf($id)
    {
        /* 还有子菜单，不能更改 */
        $query = array();
        $query['pid'] = $id;
        $num = $this->count($query);
        if ($num > 0) {
            throw new \Exception('不是末级菜单或者此菜单下还存在有菜单,您不能删除!');
        }
    }

    public function getPrivilege($menu_list, $requestUrl = "")
    {
        return $this->buildPrivilegeTree($menu_list, $requestUrl);

        $menu_list = empty($menu_list) ? array() : $menu_list;
        $priv_arr = array();
        $sort = $this->getDefaultSort();
        /* 获取权限的分组数据 */
        $query = array();
        $query['pid'] = "";
        $res = $this->findAll($query, $sort);

        foreach ($res as $rows) {
            $rows['relevance'] = "";
            $is_active = (!empty($requestUrl) && !empty($rows['url']) && (strstr($rows['url'], $requestUrl) != false)) ? true : false;
            $priv_arr[$rows['_id']] = array(
                'name' => $rows['name'],
                'icon' => $rows['icon'],
                'relevance' => $rows['relevance'],
                'url' => $rows['url'],
                'is_show' => $rows['is_show'],
                'priv' => array(),
                'is_active' => $is_active
            );
        }

        /* 按权限组查询底级的权限名称 */
        $query = array();
        $query['pid'] = array(
            '$in' => array_keys($priv_arr)
        );
        $result = $this->findAll($query, $sort);
        foreach ($result as $priv) {
            $priv['relevance'] = "";
            $is_active = (!empty($requestUrl) && !empty($priv['url']) && (strstr($priv['url'], $requestUrl) != false)) ? true : false;
            $priv_arr[$priv["pid"]]["priv"][$priv["_id"]] = array(
                'name' => $priv['name'],
                'icon' => $priv['icon'],
                'relevance' => $priv['relevance'],
                'url' => $priv['url'],
                'is_show' => $priv['is_show'],
                'priv' => array(),
                'is_active' => $is_active
            );
            if ($is_active) {
                $priv_arr[$priv["pid"]]['is_active'] = $is_active;
            }
        }
        // 将同一组的权限使用 "," 连接起来，供JS全选
        foreach ($priv_arr as $action_id => $action_group) {
            $i = 0;
            $priv_arr[$action_id]['priv_list'] = join(',', @array_keys($action_group['priv']));

            foreach ($action_group['priv'] as $key => $val) {
                $cando = in_array($key, $menu_list) ? 1 : 0;
                $priv_arr[$action_id]['priv'][$key]['cando'] = $cando;
                if ($cando) {
                    $i++;
                }
            }

            $priv_arr[$action_id]['cando'] = ($i > 0) ? 1 : 0;
        }
        return $priv_arr;
    }

    public function buildPrivilegeTree($menu_list, $requestUrl = "")
    {
        $menu_list = empty($menu_list) ? array() : $menu_list;

        $sort = $this->getDefaultSort();

        /* 获取权限的分组数据 */
        $query = array();
        // $query['pid'] = "";
        $menus = $this->findAll($query, $sort);

        // print_r($menus);
        // die('buildPrivilegeTree');

        $parent = array();
        $new = array();
        foreach ($menus as $a) {
            unset($a['__CREATE_TIME__'], $a['__MODIFY_TIME__'], $a['__REMOVED__']);
            //$is_active = (!empty($requestUrl) && !empty($a['url']) && (strstr($a['url'], $requestUrl) != false)) ? true : false;
            $is_active = (!empty($requestUrl) && !empty($a['url']) && ($a['url'] == $requestUrl . '/list')) ? true : false;

            $cando = in_array($a['_id'], $menu_list) ? 1 : 0;
            $a['relevance'] = '';
            $privMenu = array(
                '_id' => $a['_id'],
                'name' => $a['name'],
                'icon' => $a['icon'],
                'relevance' => $a['relevance'],
                'url' => $a['url'],
                'is_show' => $a['is_show'],
                'priv' => array(),
                'is_active' => $is_active,
                'cando' => $cando,
                'priv_list' => ''
            );

            if (empty($a['pid']))
                $parent[] = $privMenu;
            $new[$a['pid']][] = $privMenu;
        }
        $tree = $this->buildTree($new, $parent);

        return $tree;
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
        foreach ($parent as $l) {
            $is_active = empty($l['is_active']) ? 0 : $l['is_active'];
            $cando = empty($l['cando']) ? 0 : $l['cando'];
            $priv_list = empty($l['priv_list']) ? '' : $l['priv_list'];
            $sub_menus = array();
            $sub_priv_list = array();
            if (isset($menus[$l['_id']])) {
                $sub_menus = $this->buildTree($menus, $menus[$l['_id']]);
                if (!empty($sub_menus)) {
                    foreach ($sub_menus as $sm) {
                        if ($sm['cando']) {
                            $cando = 1;
                        }
                        if ($sm['is_active']) {
                            $is_active = 1;
                        }
                        if (!empty($sm['priv_list'])) {
                            $sub_priv_list[] = $sm['priv_list'];
                        }
                    }
                    $sub_priv_list = array_merge($sub_priv_list, @array_keys($sub_menus));
                    $sub_priv_list = array_unique($sub_priv_list);
                }
            }
            $l['priv'] = $sub_menus;

            if (!empty($sub_priv_list)) {
                $priv_list .= join(',', $sub_priv_list);
            }
            if (!empty($priv_list)) {
                $l['priv_list'] = trim(',' . $priv_list, ',');
            }
            $l['cando'] = $cando;
            $l['is_active'] = $is_active;
            $tree[$l['_id']] = $l;
        }
        return $tree;
    }


    public function getList4Tree($menu_id = "")
    {
        $input = new Input();
        if (!empty($menu_id)) {
            $input->id = $menu_id;
        }
        $input->sort_by = "show_order";
        $input->sort_order = "asc";
        $input->page_size = 1000;

        $ret = $this->getList($input);
        $datas = array();

        foreach ($ret["data"] as $var) {
            $text = "";
            if ($var['level'] > 0) {
                $text .= str_repeat('&nbsp;', $var['level'] * 4);
            }
            $text .= $var['name'];
            $datas[$var['menu_id']] = $text;
        }
        return $datas;
    }
}
