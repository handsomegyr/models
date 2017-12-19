<?php
namespace App\Backend\Submodules\System\Models;

use App\Backend\Models\Input;

class Resource extends \App\Common\Models\System\Resource
{
    use \App\Backend\Models\Base;

    /**
     * 默认排序
     */
    public function getDefaultSort()
    {
        $sort = array(
            'module' => 1
        );
        return $sort;
    }

    /**
     * 默认查询条件
     */
    public function getQuery()
    {
        $query = array();
        return $query;
    }

    public function getPrivilege($module, $operation_list)
    {
        $list = $this->findAll(array(
            'module' => $module
        ), array(
            'controller_name' => 1,
            'action_name' => 1
        ));
        $resources = array();
        foreach ($list as $item) {
            // admin_form||表管理
            $key = "{$item['module']}_{$item['controller']}||{$item['controller_name']}";
            // [name] => 删除
            // [method] => remove
            // [key] => admin_form::remove
            $data = array();
            $data['name'] = $item['action_name'];
            $data['method'] = $item['action'];
            $data['key'] = "{$item['module']}_{$item['controller']}::{$data['method']}";
            $resources[$key][] = $data;
        }
        
        /* 获取权限的分组数据 */
        $priv_arr = array();
        foreach (array_keys($resources) as $rows) {
            $infoArr = explode("||", $rows);
            $priv_arr[$rows] = array(
                'name' => $infoArr[1],
                'relevance' => "",
                'method' => "",
                'key' => $infoArr[0]
            );
        }
        
        /* 按权限组查询底级的权限名称 */
        foreach ($resources as $key => $item) {
            foreach ($item as $priv) {
                $priv['relevance'] = "";
                $priv_arr[$key]["priv"][$priv['key']] = array(
                    'name' => $priv['name'],
                    'relevance' => $priv['relevance'],
                    'method' => $priv['method'],
                    'key' => $priv['key']
                );
            }
        }
        
        // 将同一组的权限使用 "," 连接起来，供JS全选
        foreach ($priv_arr as $action_id => $action_group) {
            $priv_arr[$action_id]['priv_list'] = join(',', @array_keys($action_group['priv']));
            foreach ($action_group['priv'] as $key => $val) {
                $priv_arr[$action_id]['priv'][$key]['cando'] = in_array($key, $operation_list) ? 1 : 0;
            }
            // 去掉错误模块
            $infoArr = explode("||", $action_id);
            if (in_array($infoArr[0], array(
                "admin_error",
                "admin_form",
                "admin_index"
            ))) {
                unset($priv_arr[$action_id]);
            }
        }
        ksort($priv_arr);
        return $priv_arr;
    }
}