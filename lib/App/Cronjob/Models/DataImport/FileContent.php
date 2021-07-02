<?php

namespace App\Cronjob\Models\DataImport;

class FileContent extends \App\Common\Models\Cronjob\DataImport\FileContent
{
    const PROCESS_STATUS_INIT = 0;
    // 已完成
    const PROCESS_STATUS_FINISH = 1;
    // 已失败
    const PROCESS_STATUS_FAIL = 2;

    public function getInfoByContentSign($content_sign, $cronjob_id, $content_type)
    {
        $query = array();
        $query['content_sign'] = $content_sign;
        $query['cronjob_id'] = $cronjob_id;
        $query['content_type'] = $content_type;
        $info = $this->findOne($query);
        return $info;
    }

    public function getFirstInfo4UnProccessByType($content_type)
    {
        // 取最前的那条数据
        $query = array();
        $query['content_type'] = $content_type;
        $query['process_status'] = self::PROCESS_STATUS_INIT;
        $list = $this->find($query, array('cronjob_id' => 1), 0, 1);
        if (!empty($list['datas'])) {
            return $list['datas'][0];
        }
        return array();
    }

    public function getContentList4UnProccess($cronjob_id, $content_type)
    {
        $query = array();
        $query['cronjob_id'] = $cronjob_id;
        $query['content_type'] = $content_type;
        $query['process_status'] = self::PROCESS_STATUS_INIT;
        $sort = array(
            'cronjob_id' => 1,
            'line_no' => 1,
            '_id' => 1,
        );
        $fileContentList = $this->findAll($query, $sort);
        return $fileContentList;
    }
    /**
     * 记录
     *
     * @param int $cron_time            
     * @param string $cronjob_id            
     * @param number $line_no            
     * @param string $content_type               
     * @param string $content          
     * @param string $content_sign            
     * @param int $now            
     * @return array
     */
    public function log($cron_time, $cronjob_id, $line_no, $content_type, $content, $content_sign, $now)
    {
        $insertData = array();
        $insertData['cron_time'] = \App\Common\Utils\Helper::getCurrentTime($cron_time);
        $insertData['cronjob_id'] = $cronjob_id;
        $insertData['returnback_cronjobId'] = '';

        $insertData['line_no'] = $line_no;
        $insertData['content_type'] = $content_type;
        $insertData['content'] = $content;
        $insertData['content_sign'] = $content_sign;

        // 处理用
        $insertData['process_status'] = 0;
        $insertData['process_num'] = 0;
        $insertData['process_time'] = \App\Common\Utils\Helper::getCurrentTime(strtotime('0001-01-01 00:00:00'));
        $insertData['process_uniqueId'] = '';
        $insertData['process_desc'] = '';

        // 回退用
        $insertData['returnback_status'] = 0;
        $insertData['returnback_num'] = 0;
        $insertData['returnback_time'] = \App\Common\Utils\Helper::getCurrentTime(strtotime('0001-01-01 00:00:00'));
        $insertData['returnback_uniqueId'] = '';

        $insertData['log_time'] = \App\Common\Utils\Helper::getCurrentTime($now);
        $result = $this->insert($insertData);

        return $result;
    }

    public function updateProcessStatus($id, $status, $now,  array $otherIncData = array(), array $otherUpdateData = array())
    {
        $updataDataOp = array();
        $updateData = array();
        $updateData['process_status'] = intval($status);
        $updateData['process_time'] = date('Y-m-d H:i:s', $now);
        if (!empty($otherUpdateData)) {
            foreach ($otherUpdateData as $key => $value) {
                $updateData[$key] = $value;
            }
        }
        $updataDataOp['$set'] = $updateData;

        if (empty($otherIncData)) {
            $otherIncData  = array();
        }
        $otherIncData = array_merge(array('process_num' => 1), $otherIncData);
        if (!empty($otherIncData)) {
            $updataDataOp['$inc'] = $otherIncData;
        }

        $affectRows = $this->update(array('_id' => $id), $updataDataOp);
        return $affectRows;
    }
}
