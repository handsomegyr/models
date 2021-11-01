<?php

namespace App\Lexiangla\Models\Contact;

class TagSync extends \App\Common\Models\Lexiangla\Contact\TagSync
{
    // 按企业微信标签信息获取乐享标签同步的信息
    public function getInfoByQyTagId($qyweixin_tagid)
    {
        // 查找乐享标签的同步表记录
        $query = array();
        $query['qyweixin_tagid'] = trim($qyweixin_tagid);

        $result = $this->findOne($query);
        return $result;
    }

    /**
     * 根据乐享标签id字符串获取企业微信标签id字符串
     *
     * @param string $tagid         乐享标签id字符串
     * @param string $separator     分隔符
     * @return string
     */
    public function getQyTagIdsStringByTagId($tagid = '', $separator = "|")
    {
        if ($tagid) {
            $arr = explode($separator, $tagid);

            $query = array();
            $query['tagid'] = array('$in' => $arr);
            $query['is_exist'] = true;
            $info = $this->findOne($query);
            if (empty($info)) {
                return '';
            }
            return implode($separator, $info['qyweixin_tagid']);
        } else {
            return '';
        }
    }
}
