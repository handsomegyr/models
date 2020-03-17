<?php

namespace App\Cronjob\Models\DataImport;

class File extends \App\Common\Models\Cronjob\DataImport\File
{
    const STATUS_INIT = 0;
    // 已上传
    const STATUS_UPLOAD = 1;
    // 已完成
    const STATUS_FINISH = 2;

    /**
     * 记录
     *
     * @param number $cron_time            
     * @param string $data_file            
     * @param string $flag_file            
     * @param string $lock_file               
     * @param number $now          
     * @param number $data_count            
     * @param number $process_total            
     * @param number $status            
     * @param string $desc            
     * @return array
     */
    public function log($cron_time, $data_file, $flag_file, $lock_file, $now, $data_count, $process_total = 0, $status = 0, $desc = '', $returnback_cronjobId = '')
    {
        $data = array();
        $data['cron_time'] = \App\Common\Utils\Helper::getCurrentTime($cron_time);
        $data['data_file'] = $data_file;
        $data['flag_file'] = $flag_file;
        $data['lock_file'] = $lock_file;
        $data['log_time'] = \App\Common\Utils\Helper::getCurrentTime($now);
        $data['data_count'] = intval($data_count);
        $data['process_total'] = intval($process_total);
        $data['status'] = intval($status);
        $data['desc'] = trim($desc);
        $data['returnback_cronjobId'] = $returnback_cronjobId;
        $result = $this->insert($data);

        return $result;
    }

    public function updateStatus($id, $status, $now,  array $otherIncData = array(), array $otherUpdateData = array())
    {
        $updataDataOp = array();
        $updateData = array();
        if (!is_null($status)) {
            $updateData['status'] = intval($status);
        }
        $updateData['log_time'] = \App\Common\Utils\Helper::getCurrentTime($now);
        if (!empty($otherUpdateData)) {
            foreach ($otherUpdateData as $key => $value) {
                $updateData[$key] = $value;
            }
        }
        $updataDataOp['$set'] = $updateData;

        if (!empty($otherIncData)) {
            $updataDataOp['$inc'] = $otherIncData;
        }

        $affectRows = $this->update(array('_id' => $id), $updataDataOp);
        return $affectRows;
    }
}
