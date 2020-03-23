<?php

namespace App\Backend\Models;

use App\Backend\Models\Input;

trait Base
{

    /**
     * 默认排序
     */
    public function getDefaultSort()
    {
        $sort = array(
            '_id' => -1
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
     * 获取列表信息
     *
     * @param Input $input            
     * @return array
     */
    public function getList(Input $input)
    {
        // print_r($input->getQuery());
        // die('getList');
        // 分页查询
        $list = $this->find($input->getQuery(), $input->getSort(), $input->getOffset(), $input->getLimit());
        /* 记录总数 */
        $input->setRecordCount($list['total']);
        $filter = $input->getFilter();
        return array(
            'data' => $list['datas'],
            'filter' => $filter,
            'page_count' => $filter['page_count'],
            'record_count' => $filter['record_count']
        );
    }

    /**
     * 获取列表信息
     *
     * @param Input $input            
     * @return array
     */
    public function getAllList(Input $input)
    {
        // 分页查询
        $list = $this->findAll($input->getQuery(), $input->getSort());
        return $list;
    }

    /**
     * 获取空行数据
     *
     * @param Input $input            
     * @return array
     */
    public function getEmptyRow(Input $input)
    {
        $data = $input->getFormData(false);
        return $data;
    }

    /**
     * 新增或更新操作
     *
     * @param Input $input            
     * @param array $row            
     */
    public function processInsertOrUpdate(Input $input, array $row = array())
    {
        if (empty($_SESSION['admin_id'])) {
            throw new \Exception('后台操作用户未登录');
        }

        if (empty($_SESSION['processInsertOrUpdate'])) {
            $_SESSION['processInsertOrUpdate'] = time();
            try {
                $data = $input->getFormData(true);
                // $this->setPhql(true);
                if (empty($row) || empty($row['_id'])) {
                    $data['__CREATE_USER_ID__'] = $_SESSION['admin_id'];
                    $data['__CREATE_USER_NAME__'] = $_SESSION['admin_name'];
                    $data['__MODIFY_USER_ID__'] = $_SESSION['admin_id'];
                    $data['__MODIFY_USER_NAME__'] = $_SESSION['admin_name'];
                    $this->insert($data);
                } else {
                    $query['_id'] = $row['_id'];
                    // $this->setDebug(true);
                    $data['__MODIFY_USER_ID__'] = $_SESSION['admin_id'];
                    $data['__MODIFY_USER_NAME__'] = $_SESSION['admin_name'];
                    $this->update($query, array(
                        '$set' => $data
                    ));
                }
            } catch (\Exception $e) {
                throw $e;
            } finally {
                unset($_SESSION['processInsertOrUpdate']);
            }
        } else {
            throw new \Exception('上次的操作未完成，请稍候再试');
        }
    }

    /**
     * 删除操作
     *
     * @param Input $input            
     * @param array $row            
     */
    public function processDelete(Input $input, array $row = array())
    {
        if (empty($_SESSION['admin_id'])) {
            throw new \Exception('后台操作用户未登录');
        }
        $query = array(
            '_id' => $input->id
        );
        // 如果是物理删除的话
        if ($this->isPhysicalRemove) {
            return $this->physicalRemove($query);
        } else {
            $updateData = array();
            $updateData['__REMOVED__'] = 1;
            $updateData['__REMOVE_USER_ID__'] = $_SESSION['admin_id'];
            $updateData['__REMOVE_USER_NAME__'] = $_SESSION['admin_name'];
            return $this->update($query, array('$set' => $updateData));
        }
    }
}
