<?php

namespace App\Lexiangla\Models\Contact;

class UserSync extends \App\Common\Models\Lexiangla\Contact\UserSync
{
    // 按企业微信成员信息获取乐享成员同步的信息
    public function getInfoByQyUserId($qyweixin_userid)
    {
        // 查找乐享成员的同步表记录
        $query = array();
        $query['qyweixin_userid'] = trim($qyweixin_userid);

        $result = $this->findOne($query);
        return $result;
    }
}
