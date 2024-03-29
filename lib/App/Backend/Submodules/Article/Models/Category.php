<?php

namespace App\Backend\Submodules\Article\Models;

use App\Backend\Models\Input;

class Category extends \App\Common\Models\Article\Category
{

    use \App\Backend\Models\Base;

    /**
     * 默认排序
     */
    public function getDefaultSort()
    {
        $sort = array(
            'sort' => 1
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

    /**
     * 获取商品分类列表信息
     *
     * @param Input $input            
     * @return array
     */
    public function getList(Input $input)
    {
        // 分页查询
        $list = $this->findAll($input->getQuery(), $input->getSort());
        $categoryList = array();
        if (!empty($list)) {
            foreach ($list as $item) {
                $pkey = "p:" . (empty($item['parent_id']) ? "0" : $item['parent_id']);
                $key = ($item['_id']);
                $categoryList[$pkey][$key] = $item;
            }
        }
        if (!empty($categoryList["p:0"])) {
            $input->setRecordCount(count($categoryList["p:0"]));
            $filter = $input->getFilter();
            $categoryList["p:0"] = array_slice($categoryList["p:0"], $filter['start'], min($filter['record_count'], $filter['page_size']));
            $datas = $this->recursiveGet($categoryList, "0", 0);
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
     * @param array $categoryList            
     * @param string $pkey            
     * @param int $level            
     * @return Ambigous <multitype:, multitype:number >
     */
    private function recursiveGet($categoryList, $pkey, $level = 0)
    {
        $list = array();
        $pkey = "p:" . $pkey;
        if (!empty($categoryList[$pkey])) {
            foreach ($categoryList[$pkey] as $key => $item) {
                $item['level'] = $level;
                $item['category_id'] = $item['_id'];
                $item['has_children'] = empty($categoryList["p:" . $item['category_id']]);

                $list[] = $item;
                $list2 = $this->recursiveGet($categoryList, $key, $level + 1);
                if (!empty($list2)) {
                    $list = array_merge($list, $list2);
                }
            }
        }
        return $list;
    }

    public function getList4Tree($category_id = "")
    {
        $input = new Input();
        if (!empty($category_id)) {
            $input->id = $category_id;
        }
        $input->sort_by = "sort";
        $input->sort_order = "DESC";
        $input->page_size = 2000;

        $ret = $this->getList($input);
        $datas = array();

        foreach ($ret["data"] as $var) {
            $text = "";
            if ($var['level'] > 0) {
                $text .= str_repeat('&nbsp;', $var['level'] * 4);
            }
            $text .= $var['name'];
            $datas[$var['category_id']] = $text;
        }
        return $datas;
    }

    /**
     * 获取所有分类列表
     *
     * @return array
     */
    public function getAll()
    {
        $query = $this->getQuery();
        $sort = $this->getDefaultSort();
        $ret = $this->findAll($query, $sort);
        $list = array();
        foreach ($ret as $item) {
            $list[$item['_id']] = $item['name'];
        }
        return $list;
    }
}
