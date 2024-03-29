<?php

namespace App\Search\Models;

class Keyword extends \App\Common\Models\Search\Keyword
{

    /**
     * 增加搜索次数
     *
     * @param int $id             
     * @param int $search_num        
     * @param int $now          
     */
    public function incSearchNumByContent($content, $search_num, $now)
    {
        $query = array();
        $query['content'] = $content;

        $updateData = array();
        $updateData['search_time'] = \App\Common\Utils\Helper::getCurrentTime($now);

        $incData = array();
        $search_num = intval($search_num);
        $incData['search_num'] = $search_num;

        $affectRows = $this->update($query, array(
            '$set' => $updateData,
            '$inc' => $incData,
        ));
        return $affectRows;
    }
}
